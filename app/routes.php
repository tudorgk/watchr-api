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
//User Profile routes
Route::model("country", "Country");

Route::get("countries", [
        "as"   => "countries/index",
        "uses" => "CountryController@index"
    ]);
Route::get("country/{country_id}", [
        "as"   => "country/show",
        "uses" => "CountryController@show"
    ]);
