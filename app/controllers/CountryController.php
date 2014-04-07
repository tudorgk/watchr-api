<?php

class CountryController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$result = Country::all();

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $result->toArray())
            ,200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $country_id
	 * @return Response
	 */
	public function show($country_id)
	{
        $validator = CustomValidator::Instance();

        if($validator->exists_in_db('country_t','country_id',$country_id)){
        $result = Country::find($country_id);

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $result->toArray())
            ,200);
	    }else{
            return Response::json(
                array(
                    "response_msg"=>"Invalid country id",
                )
                ,400);
        }
    }


}