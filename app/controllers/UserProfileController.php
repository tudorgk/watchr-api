<?php

class UserProfileController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
                    'created_date'
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