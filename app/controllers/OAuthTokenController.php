<?php

class OAuthTokenController extends \BaseController {

	/**
	 * Get a token from grant_type
	 * GET /oauth/token
	 * @internal string grant_type
	 * @return Response
	 */
	public function getToken()
	{
        $bridgedRequest  = OAuth2\HttpFoundationBridge\Request::createFromRequest(Request::instance());
        $bridgedResponse = new OAuth2\HttpFoundationBridge\Response();

        $bridgedResponse = App::make('oauth2')->handleTokenRequest($bridgedRequest, $bridgedResponse);

        return $bridgedResponse;
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /oauth/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /oauth
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /oauth/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /oauth/{id}/edit
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
	 * PUT /oauth/{id}
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
	 * DELETE /oauth/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}