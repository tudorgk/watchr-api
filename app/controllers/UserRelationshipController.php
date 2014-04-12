<?php

class UserRelationshipController extends \BaseController {

	/**
	 * Create a record with "pending" value in relationship table
	 *
	 * @return Response
	 */
	public function send_friend_request()
	{
        //get friend request relationship type id
        $friend_request_type_pending = Relationship_type::select('relationship_type_id')
          ->where('name','=','pending')->first();
        $validator = CustomValidator::Instance();

        //extracting variables
        $user_id = Input::get("user_id");
        $friend_user_id = Input::get("friend_user_id");

        if(is_null($user_id) || strcmp($user_id,"") == 0 ||
            !is_numeric($user_id) || !$validator->exists_in_db('user_profile', 'user_id',$user_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid User ID",
                )
                ,400);
        }

        if(is_null($friend_user_id) || strcmp($friend_user_id,"") == 0 ||
            !is_numeric($friend_user_id) || !$validator->exists_in_db('user_profile', 'user_id',$friend_user_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid Friend User ID",
                )
                ,400);
        }

        if(strcmp($user_id, $friend_user_id)== 0){
            return Response::json(array(
                    "response_msg"=>"User ID and Friend User ID cannot be the same",
                )
                ,400);
        }

        //check if the friend request already exists
        $result = Relationship::where(function($query) use ($user_id)
                {
                    $query->where('fk_user_1', '=', $user_id)
                        ->orWhere('fk_user_2', '=', $user_id);
                })->where(function($query) use ($friend_user_id)
                {
                    $query->where('fk_user_1', '=', $friend_user_id)
                        ->orWhere('fk_user_2', '=', $friend_user_id);
                })->first();

        if($result){
            return Response::json(array(
                    "response_msg"=>"Relationship already exists",
                )
                ,400);
        }

        $relationship = new Relationship();
        $relationship->fk_user_1 = $user_id;
        $relationship->fk_user_2 = $friend_user_id;
        $relationship->fk_relationship_type = $friend_request_type_pending->relationship_type_id;
        $relationship->save();

        //TODO: Notify user_2 for friend request
        return Response::json(
            array(
                "response_msg"=>"Friend request sent",
                "data" => $relationship->toArray())
            ,200);
	}

	/**
	 * Display the relationship types.
	 *
	 * @return Response
	 */
	public function show_all_relationship_types()
	{
        $result = Relationship_type::all();

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $result->toArray())
            ,200);
	}

	/**
	 * Respond to friend requests, block users, delete friendships
	 *
	 * @return Response
	 */
	public function modify_relationship()
	{
		$validator = CustomValidator::Instance();
        //TODO: Need to dynamically get the relationship type id. this will do for now

        //extracting variables
        $user_id = Input::get("user_id");
        $relationship_id = Input::get("relationship_id");
        $relationship_type = Input::get("relationship_type");

        if(is_null($user_id) || strcmp($user_id,"") == 0 ||
            !is_numeric($user_id) || !$validator->exists_in_db('user_profile', 'user_id',$user_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid User ID",
                )
                ,400);
        }

        if(is_null($relationship_id) || strcmp($relationship_id,"") == 0 ||
            !is_numeric($relationship_id) || !$validator->exists_in_db('relationship', 'relationship_id',$relationship_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid Relationship ID",
                )
                ,400);
        }

        switch ($relationship_type) {
            case 1:
                //pending
                return Response::json(array(
                        "response_msg"=>"Invalid relationship type",
                    )
                    ,400);
                break;
            case 2:
                //friend
                //set the relationship_type value to 2 (accept friend request)
                $relationship_entry = Relationship::find($relationship_id);
                if($relationship_entry->fk_relationship_type == 1 &&
                    $relationship_entry->fk_user_2 == $user_id){
                    $relationship_entry->fk_relationship_type = 2;
                    $relationship_entry->save();
                    return Response::json(
                        array(
                            "response_msg"=>"Friend Request Accepted",
                            "data" => $relationship_entry->toArray())
                        ,200);

                    //TODO: Notify user accepted friend request
                }else{
                    return Response::json(array(
                            "response_msg"=>"User cannot accept his own friend request. Or trying to modify a !pending friend request",
                        )
                        ,400);
                }
                break;
            case 3:
                //blocked
                //don't know yet
                break;
            case 4:
                //rejected
                //delete the request from relationship table
                $relationship_entry = Relationship::find($relationship_id);
                $relationship_entry->delete();
                return Response::json(
                    array(
                        "response_msg"=>"User rejected the friend request")
                    ,200);
                break;
            case 5:
                //deleted
                //delete friendship from relationship table
                $relationship_entry = Relationship::find($relationship_id);
                $relationship_entry->delete();
                return Response::json(
                    array(
                        "response_msg"=>"User unfriended")
                    ,200);
                break;

        }

	}

	/**
	 * Get all friend requests for user
	 *
	 * @param  int  $user_id
	 * @return Response
	 */
	public function show_friend_requests($user_id)
	{
        $validator = CustomValidator::Instance();
        //TODO: Need to dynamically get the relationship type id. this will do for now

        //extracting variables
        if(is_null($user_id) || strcmp($user_id,"") == 0 ||
            !is_numeric($user_id) || !$validator->exists_in_db('user_profile', 'user_id',$user_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid User ID",
                )
                ,400);
        }

        //TODO: Need to dynamically get the relationship type id. this will do for now
        $friend_request_array = Relationship::where('fk_user_2', '=', $user_id)
            ->where('fk_relationship_type', '=', '1')->get();

        return Response::json(
            array(
                "response_msg"=>"Friend requests array",
                "data" => $friend_request_array->toArray())
            ,200);


	}

	/**
	 * Get friend list for user
	 *
	 * @param  int  $user_id
	 * @return Response
	 */
	public function show_friends($user_id)
	{
        $validator = CustomValidator::Instance();

        if(is_null($user_id) || strcmp($user_id,"") == 0 ||
            !is_numeric($user_id) || !$validator->exists_in_db('user_profile', 'user_id',$user_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid User ID",
                )
                ,400);
        }

        $friend_list = Relationship::where(function($query) use ($user_id)
            {
                $query->where('fk_user_1', '=', $user_id)
                    ->orWhere('fk_user_2', '=', $user_id);
            })->where('fk_relationship_type' ,'=', '2')->get();


        $response_data = array();

        foreach ($friend_list as $friendship){
            $friendship_item= array();
            $friendship_item ['relationship_id'] = $friendship->relationship_id;

            //find out who is the user that requested the list
            if($friendship->fk_user_1 == $user_id){
                $user_id_2_get = $friendship->fk_user_2;
            }else{
                $user_id_2_get = $friendship->fk_user_1;
            }

            //get the friend from the database (who is active)
            $user_query = User_profile::select('user_id', 'username', 'email', 'first_name','last_name' , 'fk_photo')
                ->where('user_id', '=', $user_id_2_get)
                ->where('fk_profile_status' , '=', '1')
                ->get();

            $friendship_item['user'] = $user_query->toArray()[0];

            $response_data[]=$friendship_item;
        }

        return Response::json(
            array(
                "response_msg"=>"Friend list array",
                "data" =>$response_data)
            ,200);
	}

}