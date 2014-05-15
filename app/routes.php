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

/*
 * Authorization
 *
 */

/*
App::singleton('oauth2', function() {

        $storage = new OAuth2\Storage\Pdo(array(
            'dsn' => 'mysql:dbname=watchr_db;host=localhost',
            'username' => 'root',
            'password' => '50centrulzz'));

       $server = new OAuth2\Server($storage, array(
            'always_issue_new_refresh_token' => true,
            'refresh_token_lifetime'         => 2419200,
        ));

        $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
        $server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
        // create the grant type
        $grantType = new OAuth2\GrantType\RefreshToken($storage);
        // add the grant type to your OAuth server
        $server->addGrantType($grantType);

        return $server;
    });
*/

Route::post('oauth/access_token', function()
    {
        return AuthorizationServer::performAccessTokenFlow();
    });


//Route::get('/oauth/authorize', array('before' => 'check-authorization-params|auth', function()
//    {
//        // get the data from the check-authorization-params filter
//        $params = Session::get('authorize-params');
//
//        // get the user id
//        $params['user_id'] = Auth::user()->user_id;
//
//        // display the authorization form
////        return View::make('authorization-form', array('params' => $params));
//        echo('display form');
//
//    }));
//
//
//Route::post('/oauth/authorize', array('before' => 'check-authorization-params|auth|csrf', function()
//    {
//        // get the data from the check-authorization-params filter
//        $params = Session::get('authorize-params');
//
//        // get the user id
//        $params['user_id'] = Auth::user()->user_id;
//
//        // check if the user approved or denied the authorization request
//        if (Input::get('approve') !== null) {
//
//            $code = AuthorizationServer::newAuthorizeRequest('user', $params['user_id'], $params);
//
//            Session::forget('authorize-params');
//
//            var_dump($code);
//
//            return Redirect::to(AuthorizationServer::makeRedirectWithCode($code, $params));
//        }
//
//        if (Input::get('deny') !== null) {
//
//            Session::forget('authorize-params');
//
//            return Redirect::to(AuthorizationServer::makeRedirectWithError($params));
//        }
//    }));

//Route::post('oauth/token', [
//        "as"   => "oauth/token",
//        "uses" => "OAuthTokenController@getToken"
//    ]);
//
//Route::get("oauth/resource", [
//        "as"   => "oauth/resource",
//        "uses" => "OAuthResourceController@getResource"
//    ]);
//
//Route::get("oauth/authorize", [
//        "as"   => "oauth/authorize",
//        "uses" => "OAuthAuthorizeController@authorize"
//    ]);

Route::get('secure-route', array('before' => 'oauth', function(){
        return "oauth secured route";
    }));

//User Profile routes

Route::post("users", [
        "as"   => "user/store",
        "uses" => "UserProfileController@store"
    ]);

Route::group(array('before' => 'oauth'), function()
    {
//User Profile routes

        Route::get("users", array(
                "as"   => "user/index",
                "uses" => "UserProfileController@index"
            ));
        Route::get("users/{user_id}", [
                "as"   => "user/show",
                "uses" => "UserProfileController@show"
            ]);

        Route::post("users/update/{user_id}", [
                "as"   => "user/update",
                "uses" => "UserProfileController@update"
            ]);

        Route::post("users/delete/{user_id}", [
                "as"   => "user/destroy",
                "uses" => "UserProfileController@destroy"
            ]);



//Country routes
        Route::get("country/all", [
                "as"   => "countries/index",
                "uses" => "CountryController@index"
            ]);
        Route::get("country/{country_id}", [
                "as"   => "country/show",
                "uses" => "CountryController@show"
            ]);


//profile_statuses routes

        Route::get("profile_status/all", [
                "as"   => "profileStatuses/index",
                "uses" => "ProfileStatusController@index"
            ]);

        /*
         * device Profile routes
         */


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
        //Not implemented yet
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
        //TODO: needs tinkering a little bit
        Route::post("relationship/modify", [
                "as"   => "modifyRelationship/modify",
                "uses" => "UserRelationshipController@modify_relationship"
            ]);

        /*
         * Watchr Events routes
         */
//get Event details


        Route::get("events/active", [
                "as"   => "events/show",
                "uses" => "EventManagerController@get_active_events"
            ]);

//post a new event
        Route::post("events/new", [
                "as"   => "events/new",
                "uses" => "EventManagerController@post_new_event"
            ]);

//post a new event
        Route::post("events/new_with_media", [
                "as"   => "events/new_with_media",
                "uses" => "EventManagerController@post_new_event_with_media"
            ]);

//destroy an event
        Route::post("events/destroy/{event_id}", [
                "as"   => "events/destroy",
                "uses" => "EventManagerController@delete_event"
            ]);

        /*
         * Watchr Conversation methods
         */


//Get a conversation stream for an event. It's automatically created on event creation.
//With optional take() and skip(). Returns all the necessary data for every reply in the stream
        Route::get("events/conversation/{event_id}", [
                "as"   => "events/conversation/stream",
                "uses" => "ConversationManagerController@get_conversation_stream"
            ]);

//Post a new reply to a conversation
        Route::post("events/conversation/reply/new", [
                "as"   => "events/conversation/reply/new",
                "uses" => "ConversationManagerController@post_new_reply"
            ]);

//Delete a user's reply
        Route::post("events/conversation/reply/destroy", [
                "as"   => "events/conversation/reply/destroy",
                "uses" => "ConversationManagerController@delete_reply"
            ]);

        /*
         * Event rating methods
         *
         */


        //get rating for an event
        Route::get("events/rating", [
                "as"   => "events/rating/get",
                "uses" => "EventRatingController@get_rating_for_event"
            ]);

        //post a rating
        Route::post("events/rating", [
                "as"   => "events/rating/post",
                "uses" => "EventRatingController@post_rating"
            ]);

        //get a specific event
        Route::get("events/details/{event_id}", [
                "as"   => "events/show",
                "uses" => "EventManagerController@get_event_details"
            ]);

    });



Route::model("user", "User_profile");

Route::model("profile_status", "Profile_status");

Route::model("country", "Country");

Route::model("device", "Device");
Route::model("profile_device", "Profile_device");

Route::model("relationship", "Relationship");
Route::model("relationship_type", "Relationship_type");

Route::model("watchr_event", "Watchr_event");
Route::model("watchr_category", "Watchr_category");
Route::model("watchr_event_category", "Watchr_event_category");

Route::model("conversation", "Conversation");
Route::model("conversation_reply", "Conversation_reply");
Route::model("conversation_status", "Conversation_status");
Route::model("reply_status", "Reply_status");
