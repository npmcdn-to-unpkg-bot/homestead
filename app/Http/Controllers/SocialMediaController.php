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
	public function index($slug)
	{
        
        $catObj = Category::whereSlug($slug)->first();
       //printR($catObj);//exit;
        $catPathArr = $catObj->getCategoryPath($slug);
        
        // Check to see if we're getting members within single category
        // or members within groups of categories
        // eg. member within groups of categories = members of the Pacific division and the teams they're on
        // eg. member within single category = members (Blake Griffin et al) of the Clippers 
        $getMembersWithinSingleCategory = false;
        foreach($catPathArr as $obj) {
            if ($obj->child_id == $catObj->id) {
                $getMembersWithinSingleCategory = true;
                break;
            }
        }
 
        $memberSocialObj = new \App\MemberSocial();
        if ($getMembersWithinSingleCategory) {
            //echo $catObj->id."|";
            $contentArr = $memberSocialObj->getMembersAndSocialMediaWithinSingleCategory($catObj);
            return view('socialmedia.child', compact('contentArr', 'catPathArr'));


        }
        
        

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
