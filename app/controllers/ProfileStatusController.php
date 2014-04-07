<?php

class ProfileStatusController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $result = Profile_status::all();

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $result->toArray())
            ,200);
	}
}