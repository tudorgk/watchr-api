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
Route::post("user/update/{user_id}", [
        "as"   => "user/update",
        "uses" => "UserProfileController@update"
    ]);
Route::post("user/delete/{user_id}", [
        "as"   => "user/destroy",
        "uses" => "UserProfileController@destroy"
    ]);

//Country routes
Route::model("country", "Country");

Route::get("country/all", [
        "as"   => "countries/index",
        "uses" => "CountryController@index"
    ]);
Route::get("country/{country_id}", [
        "as"   => "country/show",
        "uses" => "CountryController@show"
    ]);


//profile_statuses routes
Route::model("profile_status", "Profile_status");

Route::get("profile_status/all", [
        "as"   => "profileStatuses/index",
        "uses" => "ProfileStatusController@index"
    ]);

/*
 * device Profile routes
 */
Route::model("device", "Device");
Route::model("profile_device", "Profile_device");

//get all the devices for the user
Route::get("device/user/{user_id}", [
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
Route::post("device/update/{device_id}", [
        "as"   => "device/update",
        "uses" => "ProfileDeviceManagerController@update"
    ]);

//remove a device
Route::post("device/delete", [
        "as"   => "device/destroy",
        "uses" => "ProfileDeviceManagerController@destroy"
    ]);



/*
 * User Relationships routes
 */
Route::model("relationship", "Relationship");
Route::model("relationship_type", "Relationship_type");

//get Relationship Types
Route::get("relationship/types", [
        "as"   => "relationshipTypes/show",
        "uses" => "UserRelationshipController@show_all_relationship_types"
    ]);


//get pending Friend Requests List
Route::get("relationship/requests/{user_id}", [
        "as"   => "relationshipFriendRequests/show",
        "uses" => "UserRelationshipController@show_friend_requests"
    ]);

//get Friend List
Route::get("relationship/friends/{user_id}", [
        "as"   => "relationshipFriends/show",
        "uses" => "UserRelationshipController@show_friends"
    ]);

//get Blocked List
Route::get("relationship/blocked/{user_id}", [
        "as"   => "relationshipBlockedUsers/show",
        "uses" => "UserRelationshipController@show_blocked_users"
    ]);

//send Friend Request to user
Route::post("relationship/request", [
        "as"   => "friendRequest/store",
        "uses" => "UserRelationshipController@send_friend_request"
    ]);

//modify Relationship Type (accept/block/ignore Request)
Route::post("relationship/modify", [
        "as"   => "modifyRelationship/modify",
        "uses" => "UserRelationshipController@modify_relationship"
    ]);

/*
 * Watchr Events routes
 */
//get Event details
Route::model("watchr_event", "Watchr_event");
Route::get("event/{event_id}", [
        "as"   => "event/show",
        "uses" => "EventManagerController@get_event_details"
    ]);
