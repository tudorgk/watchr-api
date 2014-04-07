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

Route::get("users", [
        "as"   => "user/index",
        "uses" => "UserProfileController@index"
    ]);
Route::post("users", [
        "as"   => "user/store",
        "uses" => "UserProfileController@store"
    ]);
Route::get("users/{user_id}", [
        "as"   => "user/show",
        "uses" => "UserProfileController@show"
    ]);
Route::put("users/{user_id}", [
        "as"   => "user/update",
        "uses" => "UserProfileController@update"
    ]);
Route::delete("users/{user_id}", [
        "as"   => "user/destroy",
        "uses" => "UserProfileController@destroy"
    ]);