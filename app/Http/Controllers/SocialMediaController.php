<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Redirect;
use Illuminate\Http\Request;

use App\MemberSocial;
use App\Category;
use App\Site;
use App\Member;

class SocialMediaController extends Controller {
    
    protected $rules = [
		'member_id' => ['required'],
		'social_media_id' => ['required']
	];
    
    public function __construct() {
        $this->memberSocialObj = new MemberSocial();
        $this->memberObj = new Member();
    }
    
    public function welcome()
    {
        return view('welcome');
    }
    
    public function categorylist()
    {
        
        $categoryPAndCObj = new \App\CategoryParentAndChildren();
        $category = new \App\Category;
        $categoriesObj = $category->all();
		$categoriesArr = $category->getCategoriesArr($categoriesObj);
		$parentChildArr = $categoryPAndCObj->getHierarchy();
		return view('socialmedia.categorylist', compact('categoriesObj', 'parentChildArr', 'categoriesArr'));

    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($slug )
	{
 
        if ($slug != 'all') {
            
            $catObj = Category::whereSlug($slug)->first();
            if (is_null($catObj)) {
                // TODO redirct 404
                exit('not finding category for that slug');
            }
            $catPathArr = $catObj->getCategoryPath($slug);
            $catArr = $catObj->getChildren($catObj->id);

        } else {
            
            $catPathArr = array();
            $catObj = new Category();
            $catArr = $catObj->getParents();
            
        }

        $getChildren = $this->getChildrenBool($catPathArr, $slug, $catObj);

        // eg. get the teammates on the Lakers, don't get teams in the Pacific Coast division
        if ($getChildren) {
            
            $memberArr = $this->memberObj->getMembersWithinSingleCategory($catObj->id);
            $contentArr = $this->memberSocialObj->getSocialMediaWithMemberIds($memberArr);

            return view('socialmedia.child', compact('memberArr', 'contentArr', 'catPathArr'));

        } else {
            
            $parentArr['contentArr'] = [];
            foreach($catArr as $catId => $catName) {
                $memberArr = $this->memberObj->getMembersWithinSingleCategory($catId);
                $contentArr = $this->memberSocialObj->getSocialMediaWithMemberIds($memberArr);
                $parentArr['memberArr'][$catId] = $memberArr;
                $parentArr['contentArr'] = $parentArr['contentArr'] + $contentArr;

            }

            return view('socialmedia.parent', compact('parentArr', 'catArr', 'catPathArr'));
            
        }
        
	}
    
    private function getChildrenBool($catPathArr, $slug, $catObj) 
    {
        
        $getChildren = false;
        if (Site::getCategoryDepth() == 2 && $slug != 'all') {
            // if it is just a collection of parents without children (and we're not getting 'all'),
            // treat parent as child and use child layout
            $getChildren = true;
        } else {
            // Check to see if we're getting children - members within single category
            // or parent - members within groups of categories 
            // eg. parent = members within groups of categories = members of the NBA Pacific div and the teams they're on
            // eg. children = members within single category = members (Blake Griffin et al) of the category Clippers 
            // Members in single category gets all members displayed unconcealed on page
            // Members in groups of categories get members concealed and navigable within categories on page
            foreach($catPathArr as $obj) {
                if ($obj->child_id == $catObj->id && $obj->parent_id >0 ) {
                    $getChildren = true;
                    break;
                }
            }
        }
        
        return $getChildren;
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
