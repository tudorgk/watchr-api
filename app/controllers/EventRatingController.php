<?php

class EventRatingController extends \BaseController {

    public function post_rating(){
        //getting creator ID using OAuth
        $_creator_id = ResourceServer::getOwnerId();
        $_event_id = Input::get('event_id');
        $_rating_value = Input::get('rating_value');

        $validator = Validator::make(array(
                'event_id' => $_event_id,
                'creator_id' => $_creator_id,
                'rating_value' => $_rating_value
            ),array(
                'event_id' => 'required|integer|exists:watchr_event,event_id',
                'creator_id' => 'required|integer|exists:user_profile,user_id',
                'rating_value' => 'required|integer'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        $rating_query = Rating::where('fk_user_id', '=', $_creator_id)
            ->where('fk_event_id', '=', $_event_id)
            ->first();

        if($rating_query){
            if($_creator_id != $rating_query->fk_user_id){
                return Response::json(array(
                        "error"=>"You don't have access to modify this user's rating",
                    )
                    ,400);
            }

            //if the rating exists
            $rating_query->rating_value = $_rating_value;
            $rating_query->save();
        }else{
            //if it does not create a new rating record
            $new_rating = new Rating();
            $new_rating->fk_user_id = $_creator_id;
            $new_rating->fk_event_id = $_event_id;
            $new_rating->rating_value = $_rating_value;
            $new_rating->save();
        }

        $event_rating_controller = new EventRatingController();

        return $event_rating_controller->get_rating_for_event($_event_id);

    }

    public function get_rating_for_event($_event_id = null){

        $_creator_id = ResourceServer::getOwnerId();

        if($_event_id == null){
            //getting creator ID using OAuth
            $_event_id = Input::get('event_id');
        }

        $validator = Validator::make(array(
                'event_id' => $_event_id,
                'creator_id' => $_creator_id
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

        //get the rating sum for the event
        $ratingValue = Rating::where('fk_event_id','=',$_event_id)->sum('rating_value');

        //get the creator's rating it it exists
        $rating_query = Rating::where('fk_user_id', '=', $_creator_id)
            ->where('fk_event_id', '=', $_event_id)
            ->first();

        $response_array = array();

        $response_array['eventRating'] = $ratingValue;

        if($rating_query){
            $response_array['userVoted'] = 1;
            $response_array['userVoteValue'] = $rating_query->rating_value;
        }else{
            $response_array['userVoted'] = 0;
        }

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $response_array)
            ,200);

    }

}