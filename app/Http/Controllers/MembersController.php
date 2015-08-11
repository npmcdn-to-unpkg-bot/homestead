<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Member;
use App\Category;
use App\CategoryParentAndChildren;
use Input;
use Redirect;
use Illuminate\Http\Request;

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
	public function create(Member $memberObj)
	{

	    $memberObj->id = 0;
	    $memberSocialIdArr = $memberObj->getMemberSocialIdArr($memberObj->id);
	    
	   	$parentChildArr = $this->categoryPAndCObj->getHierarchy();
	    $categoriesArr = $this->categoryObj->getCategoriesArr();
	    $memberCategoryIdArr = array();
	    
        return view('members.create', compact('memberObj', 'memberSocialIdArr', 'parentChildArr', 'categoriesArr', 'memberCategoryIdArr'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Member $memberObj)
	{
	 
    	$inputArr = Input::all();
    	$socialSiteArr = $inputArr['site'];
        $categoryIdArr = $inputArr['category_id'];
    	$inputArr = array_except($inputArr, array('site', 'category_id'));
    	$memberObj = $memberObj->create( $inputArr );
    	$memberObj->saveMemberSocialIds($socialSiteArr, $memberObj->id);
        $memberObj->saveMemberCategoryIds($categoryIdArr, $memberObj->id);

    	return Redirect::route('members.edit', [$memberObj->id])->with('message', 'Member added.');
		
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
	public function edit(Member $memberObj)
	{

	    $memberSocialIdArr = $memberObj->getMemberSocialIdArr($memberObj->id);
	    $parentChildArr = $this->categoryPAndCObj->getHierarchy();
	    $categoriesArr = $this->categoryObj->getCategoriesArr();

	    $memberCategoryIdArr = $memberObj->getMemberCategoryIdArr($memberObj->id);
	    
        return view('members.edit', 
                compact('memberObj', 'memberSocialIdArr', 'parentChildArr', 'memberCategoryIdArr', 'categoriesArr'));
 
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Member $memberObj, Request $request)
	{
            
        $this->validate($request, $this->rules); 
        $inputArr = Input::all();
        $socialSiteArr = $inputArr['site'];
        $categoryIdArr = isset($inputArr['category_id']) ? $inputArr['category_id'] : array();
        $memberObj->saveMemberCategoryIds($categoryIdArr, $memberObj->id);
        $inputArr = array_except($inputArr, '_method', 'site', 'category_id');
        $memberObj->update($inputArr);
        $memberObj->saveMemberSocialIds($socialSiteArr, $memberObj->id);

    	return Redirect::route('members.edit', [$memberObj->id])->with('message', 'Member updated.');

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Member $memberObj)
	{

        $memberObj->delete();
        \DB::table('member_categories')->where('member_id', '=', $memberObj->id)->delete();
        \DB::table('member_social_ids')->where('member_id', '=', $memberObj->id)->delete();
        \DB::table('social_media')->where('member_id', '=', $memberObj->id)->delete();
 
	    return Redirect::route('members.index')->with('message', 'Member deleted.');
	}

}
