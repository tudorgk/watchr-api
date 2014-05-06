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

        $validator = CustomValidator::Instance();
        if(!$validator->is_valid_id('watchr_event', 'event_id' , $event_id)){
            return Response::json(array(
                    "response_msg"=>"Invalid event id",
                )
                ,400);
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
                ->oldest()
                ->skip($_skip)
                ->take($_count)
                ->get();

        } else{
            //get all the "ok" (fk_reply_status == 1) replies to the conversation.
            //join all the profile user data
            $reply_query = $conversation->replies()
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



}