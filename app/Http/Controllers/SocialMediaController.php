<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Redirect;
use Illuminate\Http\Request;

use App\MemberSocial;
use App\Category;

class SocialMediaController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($slug)
	{
        
        // TODO enable 'disable' of member's social_id so social_media is not selected for that social_id and member 
        
        $catObj = Category::whereSlug($slug)->first();
       //printR($catObj);//exit;
        $catPathArr = $catObj->getCategoryPath($slug);
        
        // Check to see if we're getting members within single category
        // or members within groups of categories
        // eg. members within groups of categories = members of the Pacific division and the teams they're on
        // eg. members within single category = members (Blake Griffin et al) of the category Clippers 
        // Members in single category gets all members displayed unconcealed on page
        // Members in groups of categories get members concealed and scrollable within categories on page
        $getMembersWithinSingleCategory = false;
        foreach($catPathArr as $obj) {
            if ($obj->child_id == $catObj->id && $obj->parent_id >0 ) {
                $getMembersWithinSingleCategory = true;
                break;
            }
        }

        $memberSocialObj = new MemberSocial();
        if ($getMembersWithinSingleCategory) {
            $memberArr = $memberSocialObj->getMembersWithinSingleCategory($catObj);
            $contentArr = $memberSocialObj->getSocialMediaWithMemberIds($memberArr);
            return view('socialmedia.child', compact('memberArr', 'contentArr', 'catPathArr'));

        }
        
	}

}
