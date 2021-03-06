<?php

class UserProfileController extends \BaseController {

	/**
	 * Get logged in user's details
	 *
	 * @return Response
	 */
	public function get_logged_in_user()
	{
        //get owner id from OAuth
        $ownerId = ResourceServer::getOwnerId();

        $logged_in_user = User_profile::find($ownerId);

        $response_array = $logged_in_user->toArray();

        $profile_photo = $logged_in_user->photo()->get()->first();

        if(is_null($profile_photo)){
            $response_array['profile_photo'] = null;
        }else{
            $response_array['profile_photo'] = $profile_photo->toArray();
        }

//        //TODO: Testing push notification
//        PushNotification::app('watchrIOS')
//            ->to('22056182836e7bb18221d294b3b1cfcffab69453c23b86c59dbe5abba5999d69')
//            ->send('Hello World, i`m a push message');


        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $response_array)
            ,200);

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

        $profile_photo = Input::file('profile_photo');

        $first_name = Input::get('first_name');
        $last_name = Input::get('last_name');

        //sanity checks
        $validator = Validator::make(
            array(
                'username' => Input::get("username"),
                'email' => Input::get("email"),
                'password' => Input::get("password"),
                'country' => Input::get("country"),
                'gender' => Input::get("gender"),
                'first_name' => $first_name,
                'last_name' => $last_name,
                'profile_photo' => $profile_photo
            ),
            array(
                'username' => 'required|unique:user_profile,username',
                'password' => 'required|min:8',
                'email' => 'required|email|unique:user_profile,email',
                'country' => 'required|integer|exists:country_t,country_id',
                'gender' => 'required|integer',
                'first_name' => 'required',
                'last_name' => 'required',
                'profile_photo' => 'mimes:jpeg,png'
            )
        );

        if($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }


        //creating a new user
        $user = new User_profile();
        $user->username = Input::get("username");
        $user->fk_country = Input::get("country");
        $user->fk_profile_status = 1;
        $user->gender = Input::get("gender");
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = Input::get("email");
        $user->password = Hash::make(Input::get("password"));
        $user->save();

        //if the user registered a profile photo
        if(!is_null($profile_photo)){

            //set up the destination path
            $destinationPath = public_path(). '/profile_photos/'. $user->user_id . '/';

            $user_profile_photo = new User_photo();
            $user_profile_photo->location = '/profile_photos/'. $user->user_id . '/'.$profile_photo->getClientOriginalName();
            $user_profile_photo->file_type = $profile_photo->getMimeType();
            $user_profile_photo->filename= $profile_photo->getClientOriginalName();
            $user_profile_photo->save();

            //move the file to the specified location
            $profile_photo->move($destinationPath,$profile_photo->getClientOriginalName());

            //bind the profile photo with the user
            $user->fk_photo = $user_profile_photo->photo_id;
            $user->save();

        }

        return Response::json(array(
                "response_msg"=>"User Created",
            )
            ,201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $_user_id
	 * @return Response
	 */
	public function show($_user_id)
    {
        $validator = Validator::make(array(
                'userId' => $_user_id
            ),array(
                'userId' => 'required|integer|exists:user_profile,user_id'
            ));

        if($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        //get owner id from OAuth
        $ownerId = ResourceServer::getOwnerId();

        $user = User_profile::find($_user_id);
        $response_array = $user->toArray();

        $user_profile_photo = $user->photo()->get()->first();

        if(!is_null($user_profile_photo))
            $response_array['profile_photo'] = $user_profile_photo->toArray();

        return Response::json(
                array(
                    "message"=>"Request Ok",
                    "data" => $response_array)
                ,200);

    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}