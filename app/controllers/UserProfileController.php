<?php

class UserProfileController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $users = User_profile::all()->take(25);

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $users->toArray())
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

        //sanity checks
        $validator = Validator::make(
            array(
                'username' => Input::get("username"),
                'email' => Input::get("email"),
                'password' => Input::get("password"),
                'country' => Input::get("country"),
                'gender' => Input::get("gender")
            ),
            array(
                'username' => 'required|unique:user_profile,username',
                'password' => 'required|min:8',
                'email' => 'required|email|unique:user_profile,email',
                'country' => 'required|integer|exists:country_t,country_id',
                'gender' => 'required|integer'
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
        $user->first_name = Input::get("first_name");
        $user->email = Input::get("email");
        $user->password = Hash::make(Input::get("password"));
        $user->save();

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

        $user = User_profile::where('user_id',$_user_id)->get(array(
                    'user_id',
                    'username',
                    'email',
                    'first_name',
                    'last_name',
                    'created_at'
                ));


        return Response::json(
                array(
                    "message"=>"Request Ok",
                    "data" => $user->toArray())
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