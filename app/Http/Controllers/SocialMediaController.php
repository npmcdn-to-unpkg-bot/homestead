<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Redirect;
use Illuminate\Http\Request;

use App\MemberSocial;
use App\Category;

class SocialMediaController extends Controller {
    
    protected $rules = [
		'member_id' => ['required'],
		'social_media_id' => ['required']
	];
    
    public function __construct() {
        $this->memberSocialObj = new MemberSocial();
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($slug)
	{

        if ($slug == '') {
            exit('Slug cannot be empty');
        }
        $catObj = Category::whereSlug($slug)->first();
        
        if (is_null($catObj)) {
            exit('not finding category for that slug');
        }
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


        if ($getMembersWithinSingleCategory) {
            
            $memberArr = $this->memberSocialObj->getMembersWithinSingleCategory($catObj);
            $contentArr = $this->memberSocialObj->getSocialMediaWithMemberIds($memberArr);
            return view('socialmedia.child', compact('memberArr', 'contentArr', 'catPathArr'));

        } else {
            
            
            
        }
        
	}
    
    /*
     * ajax call to get member's social media
     */
    public function getMemberSocialMedia( Request $request) 
    {
  
        $this->validate($request, $this->rules);	    
    	$input = Input::all();
        $obj = new \stdClass();
        $obj->id = $input['member_id'];
        $socialMediaId = $input['social_media_id'];
        $memberArr = array('id' => $obj);
        $memberContentArr = $this->memberSocialObj->getSocialMediaWithMemberIds($memberArr, $socialMediaId);
        return response()->json(['memberContentArr' => $memberContentArr]);
    	
        
    }
   

}
