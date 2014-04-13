<?php

class EventManagerController extends \BaseController {

	/**
	 * Get event details
	 *
	 * @param  int  $event_id
	 * @return Response
	 */
	public function get_event_details($event_id)
	{
        $validator = CustomValidator::Instance();

        if(is_null($event_id) || strcmp($event_id,"") == 0 ||
            !is_numeric($event_id) || !$validator->exists_in_db('watchr_event', 'event_id',$event_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid Event ID",
                )
                ,400);
        }

        //retrieve all the data
        $event_query = Watchr_event::leftJoin('watchr_event_status', 'watchr_event.fk_event_status', '=', 'watchr_event_status.status_id')
            ->leftJoin('position', 'watchr_event.fk_location', '=', 'position.position_id')
            ->where('watchr_event.event_id', '=', $event_id)
            ->first();


        //setting up the response array
        $response_array = $event_query->toArray();

        //get the creator of the post
        $creator_query = User_profile::select('user_id', 'username', 'email', 'first_name','last_name' , 'fk_photo')
            ->where('user_id', '=', $event_query->fk_created_by_user)
            ->where('fk_profile_status' , '=', '1')
            ->get();

        $response_array['creator'] = $creator_query->toArray();

        return Response::json(array(
                "response_msg"=>"Request Ok",
                "data" => $response_array
            )
            ,200);

	}

    /**
     * Create a new event
     *
     *
     * @return Response
     */
    public function post_new_event(){

        $validator = CustomValidator::Instance();

        //getting the required variables from POST
        $_event_name= Input::get("event_name");
        $_event_description = Input::get("event_description");

        //setting the default category value to 5 = Not Known
        $_event_category_array = array();
        $_event_category_array[] = 5;

        if (Input::get("categories")){
            $_event_category_array = json_decode(Input::get("categories"));
        }

        //TODO: Need to get the user id automatically using OAuth. Basic POST parameter will do for now
        $_creator_id = Input::get("creator_id");

        //NOT Optional: position
        $_latitude = Input::get("latitude");
        $_longitude = Input::get("longitude");

        if(is_null($_event_name) || strcmp($_event_name,"") == 0 ||
            strlen($_event_name) > 100 ){
            return Response::json(array(
                    "response_msg"=>"Invalid event name",
                )
                ,400);
        }

        if(is_null($_creator_id) || strcmp($_creator_id,"") == 0 ||
            !is_numeric($_creator_id) || !$validator->exists_in_db('user_profile', 'user_id',$_creator_id)){
            return Response::json(array(
                    "response_msg"=>"Invalid creator_id",
                )
                ,400);
        }


        //TODO: Must make position optional
        if(is_null($_latitude) || strcmp($_latitude,"") == 0 ||
            is_null($_longitude) || strcmp($_longitude,"") == 0 ){
            return Response::json(array(
                    "response_msg"=>"Invalid position",
                )
                ,400);
        }

        $new_event_record = new Watchr_event();
        $new_event_record->event_name = $_event_name;
        $new_event_record->description = $_event_description;
        $new_event_record->fk_created_by_user =$_creator_id;

        //TODO: Check binding with event status table for "open"
        $new_event_record->fk_event_status = 1;

        //Create a new position record in position table
        $new_position_record = new Position ();
        $new_position_record->latitude = $_latitude;
        $new_position_record->longitude =$_longitude;
        $new_position_record->save();

        $new_event_record->fk_location = $new_position_record->position_id;
        $new_event_record->save();

        //TODO: Check categories dynamically for ID. Ignore duplicate entries
        //set-up event_category table
        foreach ($_event_category_array as $category){

            //it the category id isn't valid place it in 5 Not Known
            if(!$validator->exists_in_db('watchr_category', 'category_id',$category)){
                $category = 5;
            }

            $new_event_category = new Watchr_event_category ();
            $new_event_category->fk_event = $new_event_record->event_id;
            $new_event_category->fk_category = $category;
            $new_event_category->save();
        }

        return Response::json(array(
                "response_msg"=>"Request Ok",
                "data" => $new_event_record->toArray()
            )
            ,201);
    }

    public function post_new_event_with_media(){
        $response = $this->post_new_event();

        //if the sanity checks fail
        if($response->getStatusCode() == 400){
            return $response;
        }

        $response_data = $response->getData();

        //get all the files from the input
        $allFiles = Input::file('media');

        if(empty($allFiles)){
            return Response::json(array(
                    'Error uploading file. File array is empty'
                ), 400);
        }

        //set up the destination path
        $destinationPath = public_path(). '/uploads/'. $response_data->data->event_id . '/';

        //TODO: Verify Mime Types
        foreach($allFiles as $file){

            //create an attachment record
            $new_attachment_record = new Attachment();
            $new_attachment_record->location = $destinationPath;
            $new_attachment_record->attachment_type = $file->getMimeType();
            $new_attachment_record->filename= $file->getClientOriginalName();
            $new_attachment_record->size = $file->getSize();
            $new_attachment_record->save();

            //move the file to the specified location
            $file->move($destinationPath,$file->getClientOriginalName());

            //bind the attachment with the event
            $new_ev_attach_record = new Event_attachment();
            $new_ev_attach_record->fk_event = $response_data->data->event_id;
            $new_ev_attach_record->fk_attachment = $new_attachment_record->id;
            $new_ev_attach_record->save();
        }

        return Response::json(array(
                'Event with media uploaded successfully'
            ), 201);

    }

    public function delete_event($event_id){

        $validator = CustomValidator::Instance();
        if(!$validator->is_valid_id('watchr_event', 'event_id' , $event_id)){
            return Response::json(array(
                    "response_msg"=>"Invalid event id",
                )
                ,400);
        }

        //doesn't actually delete it.. just sets the event_status to 2->delete
        $event_record_2_delete = Watchr_event::find($event_id);

        //TODO: get event_status dynamically
        $event_record_2_delete->fk_event_status = 2;
        $event_record_2_delete->save();

        return Response::json(array(
                "response_msg"=>"Event deleted",
                "data" => $event_record_2_delete->toArray()
            )
            ,400);

    }

    public function get_active_events($name = "nume", $id = 999, $something= "ceva"){
       $page = Input::get("name");

        dd($page);
    }

}