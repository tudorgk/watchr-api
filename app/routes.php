<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

//User Profile routes
Route::model("user", "User_profile");

Route::get("user/all", [
        "as"   => "user/index",
        "uses" => "UserProfileController@index"
    ]);
Route::get("user/{user_id}", [
        "as"   => "user/show",
        "uses" => "UserProfileController@show"
    ]);
Route::post("user", [
        "as"   => "user/store",
        "uses" => "UserProfileController@store"
    ]);
Route::put("user/{user_id}", [
        "as"   => "user/update",
        "uses" => "UserProfileController@update"
    ]);
Route::delete("users/{user_id}", [
        "as"   => "user/destroy",
        "uses" => "UserProfileController@destroy"
    ]);

//Country routes
Route::model("country", "Country");

Route::get("countries", [
        "as"   => "countries/index",
        "uses" => "CountryController@index"
    ]);
Route::get("country/{country_id}", [
        "as"   => "country/show",
        "uses" => "CountryController@show"
    ]);


//profile_statuses routes
Route::model("profile_status", "Profile_status");

Route::get("profileStatuses", [
        "as"   => "profileStatuses/index",
        "uses" => "ProfileStatusController@index"
    ]);

/*
 * device Profile routes
 */
Route::model("device", "Device");
Route::model("profile_device", "Profile_device");

//get all the devices for the user
Route::get("devicesForUser/{user_id}", [
        "as"   => "devicesForUser/show",
        "uses" => "ProfileDeviceManagerController@show_users_devices"
    ]);

//show a device for a particular user
Route::get("device/{device_id}", [
        "as"   => "device/show",
        "uses" => "ProfileDeviceManagerController@show"
    ]);

//add another device and bind it with the user
Route::post("device", [
        "as"   => "device/store",
        "uses" => "ProfileDeviceManagerController@store"
    ]);


//update device credentials
Route::put("device/{device_id}", [
        "as"   => "device/update",
        "uses" => "ProfileDeviceManagerController@update"
    ]);

//remove a device
Route::delete("device/{device_id}", [
        "as"   => "device/destroy",
        "uses" => "ProfileDeviceManagerController@destroy"
    ]);



