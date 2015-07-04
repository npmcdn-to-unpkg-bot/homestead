<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SocialMedia;
use App\Category;
use Input;
use Redirect;

use Illuminate\Http\Request;

class SocialMediaController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Category $category)
	{
		dd('hi');//
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
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Category $category
	 * @return Response
	 */
	public function show(Category $category)
	{

	    
	    
	    
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  SocialMedia $SocialMedia
	 * @return Response
	 */
	public function edit(SocialMedia $SocialMedia)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  SocialMedia $SocialMedia
	 * @return Response
	 */
	public function update(SocialMedia $SocialMedia)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  SocialMedia $SocialMedia
	 * @return Response
	 */
	public function destroy(SocialMedia $SocialMedia)
	{
		//
	}

}
