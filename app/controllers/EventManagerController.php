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



}