<?php namespace App\Http\Controllers;

//TODO look at Request
use App\Http\Requests;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\CategoryParentAndChildren;
use Input;
use Redirect;

use App\Member;
use App\Category;
use App\MemberEntity;

class MembersController extends Controller {
    
    protected $rules = [
		'name' => ['required', 'min:3'],
	];
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->categoryObj = new Category();
        $this->categoryPAndCObj = new CategoryParentAndChildren();
        $this->memberObj = new Member();
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($slug = '')
	{

        $catPathArr = array();
        $inputArr = Input::all();
        $limit = 15;
        $next = isset($inputArr['next']) ? (int)$inputArr['next'] : false;
        $search = '';
        
        if ($slug == 'search') {
            $search = $inputArr['search'];
            $membersObj = $this->memberObj->orderBy('created_at', 'desc')
                ->where('name', 'like', '%' . $search . '%')
                ->skip($next)
                ->take($limit)
                ->get();
        }else if ($slug == 'nochild') {
            $membersObj = $this->memberObj->getNoChild($next, $limit);
        } else if ($slug == 'uncategorized') {
            $membersObj = $this->memberObj->getNoCategory($next, $limit);
        } else if ($slug) {
            $membersObj = $this->memberObj->getMembersWithSlugSimple($slug, $next, $limit);
            $catPathArr = $this->categoryObj->getCategoryPath($slug);
        } else {
            $membersObj = $this->memberObj->orderBy('created_at', 'desc')->skip($next)->take($limit)->get();

        }

        $prev = false;
        if ($next >= $limit) {
            $prev = $next - $limit;
        }

        if ($membersObj->count() >= $limit) {
            $next = $next + $limit;
        } else {
            $next = false;
        }

        $categoriesObj = $this->categoryObj->all();
        $categoriesArr = $this->categoryObj->getCategoriesArr($categoriesObj);
		$parentChildArr = $this->categoryPAndCObj->getHierarchy();
        
        return view('members.index', compact('search', 'membersObj', 'next', 'prev', 'parentChildArr', 'categoriesArr', 'catPathArr'));
 
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(MemberEntity $memberEnt)
	{

	    $memberEnt->id = 0;
	    $memberSocialIdArr = $this->memberObj->getMemberSocialIdArr($memberEnt->id);
	    
	   	$parentChildArr = $this->categoryPAndCObj->getHierarchy();
	    $categoriesArr = $this->categoryObj->getCategoriesArr();
	    $memberCategoryIdArr = array();
	    
        return view('members.create', compact('memberEnt', 'memberSocialIdArr', 'parentChildArr', 'categoriesArr', 'memberCategoryIdArr'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(MemberEntity $memberEnt)
	{

    	$inputArr = Input::all();

    	$socialSiteArr = $inputArr['site'];
        $categoryIdArr = [];
        if (isset($inputArr['category_id'])) {
            $categoryIdArr = $inputArr['category_id'];
        }
        $instagramLocationId = $inputArr['instagram_location_id'];
    	$inputArr = array_except($inputArr, array('site', 'category_id', 'instagram_location_id'));
        $memberEnt = $memberEnt->init($inputArr)->insertMember();
    	$this->memberObj->saveMemberSocialIds($socialSiteArr, null, $memberEnt->id);
        if (!empty($categoryIdArr)) {
            $this->memberObj->saveMemberCategoryIds($categoryIdArr, $memberEnt->id);
        }
        
        
    	return Redirect::route('members.edit', [$memberEnt->id])->with('message', 'Member added.');
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Member $memberObj)
	{
       exit('in show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(MemberEntity $memberEnt)
	{

	    $memberSocialIdArr = $this->memberObj->getMemberSocialIdArr($memberEnt->id);
	    $parentChildArr = $this->categoryPAndCObj->getHierarchy();
	    $categoriesArr = $this->categoryObj->getCategoriesArr();
        $instagramLocationId = $this->memberObj->getInstagramLocationId($memberEnt->id);

	    $memberCategoryIdArr = $this->memberObj->getMemberCategoryIdArr($memberEnt->id);

        return view('members.edit', 
                compact('memberEnt', 'memberSocialIdArr', 'parentChildArr', 'memberCategoryIdArr', 'categoriesArr'
                , 'instagramLocationId'));
 
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(MemberEntity $memberEnt, Request $request)
	{
        
        $this->validate($request, $this->rules); 
        $inputArr = Input::all();
        $instagramLocationId = $inputArr['instagram_location_id'];
        $socialSiteArr = $inputArr['site'];
        $primaryAvatar = !empty($inputArr['primary_avatar']) ? $inputArr['primary_avatar'] : '';

        $categoryIdArr = isset($inputArr['category_id']) ? $inputArr['category_id'] : array();
        $this->memberObj->saveMemberCategoryIds($categoryIdArr, $memberEnt->id);
        $inputArr = array_except($inputArr, '_method', 'site', 'category_id');

        $ent = $memberEnt->init($inputArr)->updateMember();

        $this->memberObj->saveMemberSocialIds($socialSiteArr, $primaryAvatar, $memberEnt->id);
        $this->memberObj->saveInstagramLocationId($memberEnt->id, $instagramLocationId);
        
    	return Redirect::route('members.edit', [$memberEnt->id])->with('message', 'Member updated.');

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(MemberEntity $memberEnt)
	{

        \DB::transaction(function() use($memberEnt)
        {
            
            \DB::table('member_categories')->where('member_id', '=', $memberEnt->id)->delete();
            \DB::table('member_social_ids')->where('member_id', '=', $memberEnt->id)->delete();
            \DB::table('social_media')->where('member_id', '=', $memberEnt->id)->delete();
            \DB::table('instagram_location_ids')->where('member_id', '=', $memberEnt->id)->delete();

            \DB::table('members')->where('id', '=', $memberEnt->id)->delete();
        
        });
 
	    return Redirect::route('members.index')->with('message', 'Member deleted.');
	}

}
