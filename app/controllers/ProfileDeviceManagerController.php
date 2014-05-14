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

        //get owner id from OAuth
        $ownerId = ResourceServer::getOwnerId();

        $validator = CustomValidator::Instance();

        //extracting variables
        $brand = Input::get("brand");
        $user_id = Input::get("user_id");
        $model = Input::get("model");
        $device_uid = Input::get("device_uid");
        $device_token = Input::get("device_token");

        //sanity checks

        $validator = Validator::make(
            array(
                'brand' => $brand,
                'user_id' => $user_id,
                'model' => $model,
                'device_uid' => $device_uid
            ),
            array(
                'brand' => 'required',
                'user_id' => 'required|integer|exists:user_profile,user_id',
                'model' => 'required',
                'device_uid' => 'required|unique:device,device_id',
            )
        );

        if($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        if($ownerId != $user_id){
            return Response::json(array(
                    "error"=>"You don't have access to add devices to this user",
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

        $validator = Validator::make(
            array(
                'device_id' => $device_id,
            ),
            array(
                'device_id' => 'required|integer|exists:device,device_id|exists:profile_device,fk_device',
            )
        );

        if($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        //get owner id from OAuth
        $ownerId = ResourceServer::getOwnerId();

        $profile_device = Profile_device::where('fk_device','=',$device_id)->first();

        if($profile_device->fk_user_profile != $ownerId){
            return Response::json(array(
                    "error"=>"You don't have access to view the device for this user",
                )
                ,400);
        }

        $result = Device::find($device_id);
        return Response::json(
            array(
                "response_msg"=>"Request Ok",
                "data" => $result->toArray())
            ,200);

	}

    /**
     * Display the specified resource.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function show_users_devices($user_id)
    {
        $validator = Validator::make(array(
                'user_id' => $user_id
            ),array(
                'user_id' => 'required|integer|exists:user_profile,user_id'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        //get owner id from OAuth
        $ownerId = ResourceServer::getOwnerId();

        if($ownerId != $user_id){
            return Response::json(array(
                    "error"=>"You don't have access to add devices to this user",
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

        //extracting variables
        $brand = Input::get("brand");
        $model = Input::get("model");
        $device_uid = Input::get("device_uid");
        $device_token = Input::get("device_token");

        $validator = Validator::make(array(
                'device_id' => $device_id,
                'brand' => $brand,
                'model' => $model,
                'device_uid' => $device_uid
            ),array(
                'device_id' => 'required|integer|exists:device',
                'brand' => 'required',
                'model' => 'required',
                'device_uid' => 'required|unique:device,device_id',
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        //get owner id from OAuth
        $ownerId = ResourceServer::getOwnerId();

        $profile_device = Profile_device::where('fk_device','=',$device_id)->first();

        if($ownerId != $profile_device->fk_user_profile){
            return Response::json(array(
                    "error"=>"You don't have access to update devices for this user",
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
        $device_id = Input::get('device_id');

        $validator = Validator::make(array(
                'device_id' => $device_id
            ),array(
                'device_id' => 'required|integer|exists:device|exists:profile_device,fk_device'
            ));

        if ($validator->fails()){
            return Response::json(array(
                    "error"=>$validator->messages()->all(),
                )
                ,400);
        }

        //get owner id from OAuth
        $ownerId = ResourceServer::getOwnerId();

        $profile_device = Profile_device::where('fk_device','=',$device_id)->first();

        if($ownerId != $profile_device->fk_user_profile){
            return Response::json(array(
                    "error"=>"You don't have access to update devices for this user",
                )
                ,400);
        }

        //TODO: Check database implementation for deletion constraint
        $device_record = Device::find($device_id);

        $profile_device->delete();
        $device_record->delete();


        return Response::json(array(
                "response_msg"=>"Request Ok",
            )
            ,200);

    }

}