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
        if(is_null(Input::get("username")) || strcmp(Input::get("username"),"") == 0){
            return Response::json(array(
                    "response_msg"=>"Username field empty or not set",
                )
                ,400);
        }

        if(is_null(Input::get("email")) || strcmp(Input::get("email"),"") == 0){
            return Response::json(array(
                    "response_msg"=>"Email field empty or not set",
                )
                ,400);
        }

        if(is_null(Input::get("password")) || strcmp(Input::get("password"),"") == 0){
            return Response::json(array(
                    "response_msg"=>"Password field empty or not set",
                )
                ,400);
        }

        $validator = CustomValidator::Instance();
        if($validator->exists_in_db('user_profile','username',Input::get("username"))){
            return Response::json(array(
                    "response_msg"=>"Username already exists",
                )
                ,400);
        }
        if($validator->exists_in_db('user_profile','email',Input::get("email"))){
            return Response::json(array(
                    "response_msg"=>"Email already exists",
                )
                ,400);
        }

        //creating a new user
        $user = new User_profile();
        $user->username = Input::get("username");
        $user->fk_country = Input::get("country");
        $user->fk_gender = Input::get("gender");
        $user->first_name = Input::get("first_name");
        $user->email = Input::get("email");
        $user->password = Hash::make(Input::get("password"));
        $user->save();

        return Response::json(array(
                "response_msg"=>"Request Ok",
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
        if(is_numeric($_user_id)){
            $user = User_profile::where('user_id',$_user_id)->get(array(
                    'user_id',
                    'username',
                    'email',
                    'first_name',
                    'last_name',
                    'created_at'
                ));
        }else{
            return Response::json(array("message"=>"ID is not a numeric value"),400);
        }
        if(!$user->isEmpty())
        {
            return Response::json(
                array(
                    "message"=>"Request Ok",
                    "data" => $user->toArray())
                ,200);
        }
        else
        {
            return Response::json(array("message"=>"User not found"),404);
        }
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