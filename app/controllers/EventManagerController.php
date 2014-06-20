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

        $validator = Validator::make(array(
                'event_id' => $event_id
            ),array(
                'event_id' => 'required|integer|exists:watchr_event,event_id'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
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
            ->get()
            ->first();

        $response_array['creator'] = $creator_query->toArray();
        $creator_photo = $creator_query->photo()->get()->first();

        if(!is_null($creator_photo))
            $response_array['creator']['profile_photo'] = $creator_photo->toArray();

        //check if the event has media
        if($event_query->hasMedia){

            $media = $event_query->attachments()->get()->toArray();
            $response_array['media'] = $media;
        }

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

        //getting the required variables from POST
        $_event_name= Input::get("event_name");
        $_event_description = Input::get("event_description");

        //setting the default category value to 0 = Not Known
        $_event_category_array = array();
        $_event_category_array[] = 1;

        if (Input::get("categories")){
            $_event_category_array = Input::get("categories");
        }

        //getting creator ID using OAuth
         $_creator_id = ResourceServer::getOwnerId();

        //NOT Optional: position
        $_latitude = Input::get("latitude");
        $_longitude = Input::get("longitude");


        $validator = Validator::make(array(
                'event_name' => $_event_name,
                'event_description' => $_event_description,
                'categories' => $_event_category_array,
                'creator_id' => $_creator_id,
                'latitude' => $_latitude,
                'longitude' => $_longitude
            ),array(
                'event_name' => 'required|max:100',
                'event_description' => 'max:400',
                'categories' => 'required|array',
                'creator_id' => 'required|integer|exists:user_profile,user_id',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
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

            $category_validator = Validator::make(array(
                    'category_id' => $category
                ),array(
                    'category_id' => 'required|integer|exists:watchr_category,category_id'
                ));

            if($category_validator->fails()){
                $category = 1;
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
        //post default event with hasMedia set to 1
        $response = $this->post_new_event(1);

        //if the sanity checks fail
        if($response->getStatusCode() == 400){
            return $response;
        }

        $response_data = $response->getData();

        //get all the files from the input
        $allFiles = Input::file('media');

        $validator = Validator::make(array(
                 'media' => $allFiles
            ),array(
                'media' => 'array|required'
            ));

        if($validator->fails()){
            $this->delete_event($response_data->data->event_id);
            return Response::json(array(
                    'response_msg' =>'Error uploading file. Check file array'
                ), 400);
        }

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

            $mediavalidator = Validator::make(array(
                    'photo' => $file
                ),array(
                    'photo' => 'mimes:jpeg,png'
                ));

            if(!$mediavalidator->fails()){
                //create an attachment record
                $new_attachment_record = new Attachment();
                $new_attachment_record->location = '/uploads/'.$response_data->data->event_id . '/'.$file->getClientOriginalName();
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
            }else{
                //TODO: backtrack the uploaded attachments
                $this->delete_event($response_data->data->event_id);
                return Response::json(array(
                        'response_msg' =>'Error uploading file. Unsupported MIME type.'
                    ), 400);
            }
        }


        return Response::json(array(
                'response_msg' =>'Event with media uploaded successfully'
            ), 201);

    }

    public function delete_event($event_id){

        //getting creator ID using OAuth
        $_creator_id = ResourceServer::getOwnerId();

        $validator = Validator::make(array(
                'event_id' => $event_id,
                'creator_id' => $_creator_id,
            ),array(
                'event_id' => 'required|integer|exists:watchr_event,event_id',
                'creator_id' => 'required|integer|exists:user_profile,user_id'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }



        //doesn't actually delete it.. just sets the event_status to 2->delete
        $event_record_2_delete = Watchr_event::find($event_id);

        if($_creator_id !=  $event_record_2_delete->fk_created_by_user){
            return Response::json(array(
                    "error"=>"You don't have access to delete this user's event",
                )
                ,400);
        }

        //TODO: get event_status dynamically
        $event_record_2_delete->fk_event_status = 2;
        $event_record_2_delete->save();

        return Response::json(array(
                "response_msg"=>"Event deleted",
                "data" => $event_record_2_delete->toArray()
            )
            ,200);

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

        //order by
        $_order_by = Input::get("order_by");
        if(is_null($_order_by) || strcmp($_order_by,"") == 0){
            //if order_by is null, order by creation date
            $_order_by = 'created_at';
        }

        //order mode
        $_order_mode = Input::get("order_mode");
        if(is_null($_order_mode) || strcmp($_order_mode,"") == 0){
            //if order mode (ASC or DESC)
            $_order_mode = 'DESC';
        }



        $validator = Validator::make(array(
                'since_id' => $_since_id,
                'skip' => $_skip,
                'count' => $_count,
                'order_by' => $_order_by,
                'order_mode' => $_order_mode
            ),array(
                'since_id' => 'integer',
                'skip' => 'integer|required_with:count',
                'count' => 'integer|required_with:skip',
                'order_by' => 'in:created_at,updated_at,event_rating,distance',
                'order_mode' => 'in:ASC,DESC'
            ));

        //TODO: Encapsulate rating and distance in query

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        //geocode = json array with (latitude, longitude, radius)
        $_geocode = Input::get('geocode');
        if(is_null($_geocode)){
            $_geocode_data = null;
        }else{
            $_geocode_data = explode(",", $_geocode);
            //TODO: sanity checks for values
            if(count($_geocode_data)!=3){
                return Response::json(array(
                        "error"=>"Geocode data missing latitude/longitude/radius",
                    )
                    ,400);
            }

            $geocodeValidator = Validator::make(array(
                    'latitude' => $_geocode_data[0],
                    'longitude' => $_geocode_data[1],
                    'radius' => $_geocode_data[2]
                ),array(
                    'latitude' => 'numeric',
                    'longitude' => 'numeric',
                    'radius' => 'numeric'
                ));

            if ($geocodeValidator->fails()){
                return Response::json(array(
                        "error"=>$geocodeValidator->messages()->all(),
                    )
                    ,400);
            }
        }

        if($_geocode_data){
            $query_string =  'SELECT *,latitude, longitude, distance,(
                           SELECT SUM(r.rating_value)
                            FROM rating r
                            WHERE r.fk_event_id = E.event_id
                            ) AS event_rating
                          FROM watchr_event E, (
                        SELECT latitude, longitude,position_id,r,
                               111.045* DEGREES(ACOS(COS(RADIANS(latpoint))
                                         * COS(RADIANS(latitude))
                                         * COS(RADIANS(longpoint) - RADIANS(longitude))
                                         + SIN(RADIANS(latpoint))
                                         * SIN(RADIANS(latitude)))) AS distance
                         FROM position
                         JOIN (
                                SELECT  '.$_geocode_data[0].'  AS latpoint,  '.$_geocode_data[1].' AS longpoint, '.$_geocode_data[2].' AS r
                           ) AS p
                         WHERE latitude
                           BETWEEN latpoint  - (r / 111.045)
                               AND latpoint  + (r / 111.045)
                           AND longitude
                           BETWEEN longpoint - (r / (111.045 * COS(RADIANS(latpoint))))
                               AND longpoint + (r / (111.045 * COS(RADIANS(latpoint))))
                          ) d
                         WHERE distance <= r
                         AND position_id = E.fk_location
                         AND E.fk_event_status=1
                         ORDER BY '.$_order_by.' '.$_order_mode.'';

            if ($_count != null && $_skip!=null){
                //don't know any other way... Got to find an Eloquent query
                $query_results = DB::select(DB::raw($query_string.' LIMIT '. $_count .' OFFSET '. $_skip .' '));
            }else{
                $query_results = DB::select(DB::raw($query_string));
            }

//            var_dump($query_results);

            $events_array = array();

            foreach($query_results as $event){
                //iterate the basic query results -> need to turn them in to Watchr_event models
                $events_array[] = Watchr_event::find($event->event_id);
            }



        }else{
        //Get the active events from the database joining user, location, event status
            if ($_count != null && $_skip!=null){
                $events_query_array = Watchr_event::select(
                    array(
                        '*',
                        DB::raw('(
                           SELECT SUM(r.rating_value)
                            FROM rating r
                            WHERE r.fk_event_id = watchr_event.event_id
                            ) AS event_rating')
                    )
                )->where('fk_event_status','=', '1')->orderBy($_order_by,$_order_mode)->skip($_skip)->take($_count)->get();
            }else{
                $events_query_array = Watchr_event::select(
                    array(
                        '*',
                        DB::raw('(
                           SELECT SUM(r.rating_value)
                            FROM rating r
                            WHERE r.fk_event_id = watchr_event.event_id
                            ) AS event_rating')
                    )
                )->where('fk_event_status','=', '1')->orderBy($_order_by,$_order_mode)->get();
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

            $event_to_add = $event->toArray();

            //get the rating
            $event_to_add['rating'] = $event->getRating();

            $creator_photo = $user_query->photo()->get()->first();

            if(!is_null($creator_photo))
                $event_to_add['creator']['profile_photo'] = $creator_photo->toArray();


            //event categories
            $event_categories = $event->categories()->get();

            $first_event_category = $event_categories->first();

            if (!is_null($first_event_category)){
                $event_to_add['category'] =  $first_event_category->toArray();
            }

            //add it to the response array
            $response_array[] =$event_to_add;

        }

        //add the distance as well to the event
        if($_geocode_data){
            for($i =0 ; $i<count($events_array); $i++){
                $response_array[$i]['distance'] = $query_results[$i]->distance;
                $response_array[$i]['rating'] = $query_results[$i]->event_rating;
            }
        }else{
            //no geocode data.
        }

        return Response::json(array(
                "response_msg"=>"Requested events",
                "data" => $response_array
            )
            ,200);

    }


}