<?php

class ConversationManagerController extends \BaseController {

    /**
     * Get a conversation stream for an event. It's automatically created on event creation.
     *
     * @param $event_id
     * @return Response
     */
	public function get_conversation_stream($event_id)
	{

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

        $validator = Validator::make(array(
                'event_id' => $event_id,
                'skip' => $_skip,
                'count' => $_count
            ),array(
                'event_id' => 'required|integer|exists:watchr_event_event_id',
                'skip' => 'integer|required_with:count',
                'count' => 'integer|required_with:skip'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        //find the conversation we are looking for
        //if it does not exist create it

        $conversation = Conversation::where('fk_watchr_event', '=', $event_id)->first();

        if (count($conversation->toArray()) == 0){
            $conversation = new Conversation();
            $conversation->fk_watchr_event = $event_id;
            $conversation->fk_conversation_status = 1; // set the conversation to 1 -> OPEN
            $conversation->save();
        }

        //var_dump($conversation);

        //after we found the requested conversation record
        //retrieve the necessary replies from the users

        if ($_count != null && $_skip!=null){
            //if we have a paginated query, get the selected replies
            $reply_query = $conversation->replies()
                ->where('fk_reply_status', '=', '1')
                ->oldest()
                ->skip($_skip)
                ->take($_count)
                ->get();

        } else{
            //get all the "ok" (fk_reply_status == 1) replies to the conversation.
            //join all the profile user data
            $reply_query = $conversation->replies()
                ->where('fk_reply_status', '=', '1')
                ->oldest()
                ->get();
       }

//        var_dump($reply_query->toArray());

        $replies_response_array = array();

        foreach($reply_query as $reply_record){
            //get relevant user data for each reply
            $user_data = array();

            $user = User_profile::whereUserId($reply_record->fk_user)
                ->first();

            $user_data=$user->toArray();

            //get profile pic
            //TODO: Forgot to attach photo to user profile. oops!
            if($user->fk_photo != null){
                $user_photo = $user->photo()->first();
                $user_data['hasPhoto'] = true;
                $user_data['photoData'] = $user_photo->toArray();
            }else{
                $user_data['hasPhoto'] = false;
                $user_data['photoData'] = array();
            }

            $reply = $reply_record->toArray();
            $reply['user'] = $user_data;


            $replies_response_array [] = $reply;

        }

        return Response::json(array(
                "response_msg"=>"Conversation reply list",
                "data" => $replies_response_array
            )
            ,200);


	}

    /**
     * Post a new reply to the event's conversation.
     * @internal int user_id
     * @internal int conversation_id
     * @internal string text
     * @return Response
     */

    public function post_new_reply(){


        //getting the required variables from POST
        //get owner id from OAuth
        $_ownerId = ResourceServer::getOwnerId();
        $_conversation_id = Input::get("conversation_id");
        $_text = Input::get("text");

        $validator = Validator::make(array(
                'user_id' => $_ownerId,
                'conversation_id' => $_conversation_id,
                'text' =>$_text
            ),array(
                'user_id' => 'required|integer|exists:user_profile,user_id',
                'conversation_id' => 'required|integer|exists:conversation,conversation_id',
                'text' => 'required|max:500'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }



        //after sanity check, create a new entry in the conversation_reply table
        $reply = new Conversation_reply();
        $reply->reply_text = $_text;
        $reply->fk_conversation = $_conversation_id;
        $reply->fk_user = $_ownerId;
        $reply->fk_reply_status = 1; //it's ok
        $reply->save();

        return Response::json(array(
                "response_msg"=>"Reply posted",
                "data" => $reply->toArray()
            )
            ,201);

    }

    /**
     * Delete a reply from the event's conversation.
     * @internal int user_id
     * @internal int reply_id
     * @return Response
     */

    public function delete_reply(){


        //getting the required variables from POST
        //get owner id from OAuth
        $_ownerId = ResourceServer::getOwnerId();
        $_reply_id = Input::get("reply_id");

        $validator = Validator::make(array(
                'user_id' => $_ownerId,
                'reply_id' => $_reply_id
            ),array(
                'user_id' => 'required|integer|exists:user_profile,user_id',
                'reply_id' => 'required|integer|exists:conversation_reply,reply_id'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }


        $reply_to_delete = Conversation_reply::find($_reply_id);

        if($reply_to_delete->fk_user != $_ownerId){
            return Response::json(array(
                    "response_msg"=>"You don't have permission to delete this reply",
                )
                ,400);
        }

        $reply_to_delete->fk_reply_status = 2; //set it to delete
        $reply_to_delete->save();

        return Response::json(array(
                "response_msg"=>"Reply deleted"
            )
            ,200);
    }
}