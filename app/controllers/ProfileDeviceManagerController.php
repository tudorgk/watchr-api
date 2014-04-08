<?php

class ProfileDeviceManagerController extends \BaseController {

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

        $validator = CustomValidator::Instance();

        //extracting variables
        $brand = Input::get("brand");
        $user_id = Input::get("user_id");
        $model = Input::get("model");
        $device_uid = Input::get("device_uid");
        $device_token = Input::get("device_token");

        //sanity checks
        if(is_null($brand) || strcmp($brand,"") == 0){
            return Response::json(array(
                    "response_msg"=>"Brand field empty or not set",
                )
                ,400);
        }

        if(is_null($user_id) || strcmp($user_id,"") == 0 ||
            !is_numeric($user_id) || !$validator->exists_in_db('user_profile', 'user_id',$user_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid User ID",
                )
                ,400);
        }

        if(is_null($model) || strcmp($model,"") == 0){
            return Response::json(array(
                    "response_msg"=>"Model field empty or not set",
                )
                ,400);
        }

        if(is_null($device_uid) || strcmp($device_uid,"") == 0){
            return Response::json(array(
                    "response_msg"=>"Device UID field empty or not set",
                )
                ,400);
        }

        //TODO: Need to modify the 'already exists' checking method
        if($validator->exists_in_db('device','device_uid',$device_uid)){
            return Response::json(array(
                    "response_msg"=>"Device already exists",
                )
                ,400);
        }

        //creating a new device
        $device = new Device();
        $device->brand = $brand;
        $device->model = $model;
        $device->device_uid= $device_uid;
        $device->device_token = $device_token;
        $device->save();

        $deviceID = $device->device_id;

        //Binding the device to the user
        $profile_device_record = new Profile_device();
        $profile_device_record->fk_device = $deviceID;
        $profile_device_record->fk_user_profile = $user_id;
        $profile_device_record->save();

        return Response::json(array(
                "response_msg"=>"Request Ok",
            )
            ,200);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $device_id
	 * @return Response
	 */
	public function show($device_id)
	{

        if(is_numeric($device_id)){
            $result = Device::find($device_id);

            if($result){
                return Response::json(
                 array(
                        "response_msg"=>"Request Ok",
                        "data" => $result->toArray())
                    ,200);
            }else{
                return Response::json(
                    array(
                        "response_msg"=>"Request Ok",
                        "data" => array())
                    ,200);
            }
        }else{
            return Response::json(
                array(
                    "response_msg"=>"Invalid device id",
                )
                ,400);
        }
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function show_users_devices($user_id)
    {
        $validator = CustomValidator::Instance();

        if(is_null($user_id) || strcmp($user_id,"") == 0 ||
            !is_numeric($user_id) || !$validator->exists_in_db('user_profile', 'user_id',$user_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid User ID",
                )
                ,400);
        }

        $result = DB::table('profile_device')
            ->select('device.device_id', 'device.model', 'device.brand','device.device_uid' ,'device.device_token')
            ->join('device', 'profile_device.fk_device', '=', 'device.device_id')
            ->where('profile_device.fk_user_profile','=',$user_id)->get();

        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $result)
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
 	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $device_id
	 * @return Response
	 */
	public function update($device_id)
	{
        $validator = CustomValidator::Instance();

        if(is_null($device_id) || strcmp($device_id,"") == 0 ||
            !is_numeric($device_id) || !$validator->exists_in_db('device', 'device_id',$device_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid Device ID",
                )
                ,400);
        }

        //extracting variables
        $brand = Input::get("brand");
        $model = Input::get("model");
        $device_uid = Input::get("device_uid");
        $device_token = Input::get("device_token");

        //sanity checks
        if(is_null($brand) || strcmp($brand,"") == 0){
            return Response::json(array(
                    "response_msg"=>"Brand field empty or not set",
                )
                ,400);
        }

        if(is_null($model) || strcmp($model,"") == 0){
            return Response::json(array(
                    "response_msg"=>"Model field empty or not set",
                )
                ,400);
        }

        if(is_null($device_uid) || strcmp($device_uid,"") == 0){
            return Response::json(array(
                    "response_msg"=>"Device UID field empty or not set",
                )
                ,400);
        }

        $device = Device::find($device_id);

        $device->brand = $brand;
        $device->model = $model;
        $device->device_uid= $device_uid;
        $device->device_token = $device_token;
        $device->save();

        return Response::json(array(
                "response_msg"=>"Request Ok",
            )
            ,200);

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return Response
	 */
	public function destroy()
	{
		$validator = CustomValidator::Instance();
        $device_id = Input::get('device_id');

        if(is_null($device_id) || strcmp($device_id,"") == 0 ||
            !is_numeric($device_id) || !$validator->exists_in_db('device', 'device_id',$device_id )){
            return Response::json(array(
                    "response_msg"=>"Invalid Device ID",
                )
                ,400);
        }

        //TODO: Check database implementation for deletion constraint
        $device_record = Device::find($device_id);
        if($device_record)
            $device_record->delete();

        return Response::json(array(
                "response_msg"=>"Request Ok",
            )
            ,200);

    }

}