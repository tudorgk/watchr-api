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
     * @param int $hasMedia
     * @return Response
     */
    public function post_new_event($hasMedia = 0){

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
        $new_event_record->hasMedia = $hasMedia;
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
        $response = $this->post_new_event(1);

        //if the sanity checks fail
        if($response->getStatusCode() == 400){
            return $response;
        }

        $response_data = $response->getData();

        //get all the files from the input
        $allFiles = Input::file('media');

        if(empty($allFiles)){
            $this->delete_event($response_data->data->event_id);
            return Response::json(array(
                    'response_msg' =>'Error uploading file. File array is empty'
                ), 400);
        }else{
            $event = Watchr_event::find($response_data->data->event_id);
            $event->hasMedia = 1;
            $event->save();
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
                'response_msg' =>'Event with media uploaded successfully'
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

    public function get_active_events(){

        //optional parameters
        //since_id
        $_since_id = Input::get("since_id");
        if(is_null($_since_id) || strcmp($_since_id,"") == 0){
            $_since_id = 0;
        }

        //skip
        $_skip = Input::get("skip");
        if(is_null($_skip) || strcmp($_skip,"" ) == 0 ||  !is_numeric($_skip)){
            $_skip = null;
        }

        //count
        $_count = Input::get("count");
        if(is_null($_count) || strcmp($_count,"") == 0 ||  !is_numeric($_count)){
            //if count is 0 get ALL the posts
            $_count = null;
        }


        //geocode = json array with (latitude, longitude, radius)
        $_geocode = Input::get('geocode');
        if(is_null($_geocode)){
            $_geocode_data = null;
        }else{
            $_geocode_data = explode(",", $_geocode);
            //TODO: sanity checks for values
        }

//        var_dump($_geocode_data);

        if($_geocode_data){

            if ($_count != null && $_skip!=null){
                //don't know any other way... Got to find an Eloquent query
                $query_results = DB::select(DB::raw(
                        'SELECT * FROM watchr_event E
                            WHERE '. $_geocode_data[2] .'>
                            (SELECT (111.045* DEGREES(ACOS(COS(RADIANS('. $_geocode_data[0] .'))
                                            * COS(RADIANS(latitude))
                                            * COS(RADIANS('. $_geocode_data[1] .') - RADIANS(longitude))
                                            + SIN(RADIANS('. $_geocode_data[0] .'))
                                            * SIN(RADIANS(latitude))))) AS distance
                                FROM position
                                WHERE latitude
                                    BETWEEN '. $_geocode_data[0] .'  - ('. $_geocode_data[2] .' / 111.045)
                                        AND '. $_geocode_data[0] .'  + ('. $_geocode_data[2] .' / 111.045)
                                AND longitude
                                    BETWEEN '. $_geocode_data[1] .' - ('. $_geocode_data[2] .' / (111.045 * COS(RADIANS('. $_geocode_data[0] .'))))
                                    AND '. $_geocode_data[1] .' + ('. $_geocode_data[2] .' / (111.045 * COS(RADIANS('. $_geocode_data[0] .'))))
                                AND position_id = E.fk_location)
                            ORDER BY created_at DESC
                            LIMIT '. $_count .' OFFSET '. $_skip .' '));
            }else{
                $query_results = DB::select(DB::raw(
                        'SELECT * FROM watchr_event E
                            WHERE '. $_geocode_data[2] .'>
                            (SELECT (111.045* DEGREES(ACOS(COS(RADIANS('. $_geocode_data[0] .'))
                                            * COS(RADIANS(latitude))
                                            * COS(RADIANS('. $_geocode_data[1] .') - RADIANS(longitude))
                                            + SIN(RADIANS('. $_geocode_data[0] .'))
                                            * SIN(RADIANS(latitude))))) AS distance
                                FROM position
                                WHERE latitude
                                    BETWEEN '. $_geocode_data[0] .'  - ('. $_geocode_data[2] .' / 111.045)
                                        AND '. $_geocode_data[0] .'  + ('. $_geocode_data[2] .' / 111.045)
                                AND longitude
                                    BETWEEN '. $_geocode_data[1] .' - ('. $_geocode_data[2] .' / (111.045 * COS(RADIANS('. $_geocode_data[0] .'))))
                                    AND '. $_geocode_data[1] .' + ('. $_geocode_data[2] .' / (111.045 * COS(RADIANS('. $_geocode_data[0] .'))))
                                AND position_id = E.fk_location)
                            ORDER BY created_at DESC'));
            }



//            var_dump($query_results);

            $events_array = array();

            foreach($query_results as $event){
                //iterate the basic query results -> need to turn them in to Watchr_event models
                $events_array[] = Watchr_event::find($event->event_id);
            }



        }else{
        //Get the active events from the database joining user, location, event status
        //TODO: Get rating later
            if ($_count != null && $_skip!=null){
                $events_query_array = Watchr_event::where('fk_event_status','=', '1')->orderBy('created_at','desc')->skip($_skip)->take($_count)->get();
            }else{
                $events_query_array = Watchr_event::where('fk_event_status','=', '1')->orderBy('created_at','desc')->get();
            }
            $events_array = $events_query_array->toArray();
        }



        $response_array = array();
        //get the attachments
        foreach($events_array as $event){
            $event = Watchr_event::find($event['event_id']);
            if($event['hasMedia']){
                $attachments_query = $event->attachments()->get();
                $event['attachments'] = $attachments_query->toArray();
            }else{
                $event['attachments'] = array();
            }
            $user_query = User_profile::find($event->fk_created_by_user);
            $event['creator'] = $user_query->toArray();

            //get the position
            $location = Position::find($event->fk_location);
            $event['position'] = $location->toArray();

            $response_array[] =$event->toArray();
        }

        return Response::json(array(
                "response_msg"=>"Requested events",
                "data" => $response_array
            )
            ,400);

    }


}