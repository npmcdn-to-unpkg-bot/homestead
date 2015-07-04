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
		'first_name' => ['required', 'min:3'],
	];

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $membersObj = Member::all();
        return view('members.index', compact('membersObj'));	
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Member $memberObj)
	{
	    $memberObj->id = 0;
	    $memberSocialIdArr = Member::getMemberSocialIdArr($memberObj->id);
	    
	   	$parentChildArr = CategoryParentAndChildren::getHierarchy();
	    $categoriesArr = Category::getCategoriesArr();
	    $memberCategoryIdArr = array();
	    
        return view('members.create', compact('memberObj', 'memberSocialIdArr', 'parentChildArr', 'categoriesArr', 'memberCategoryIdArr'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
	 
    	$inputArr = Input::all();
    	$socialSiteArr = $inputArr['site'];
        $categoryIdArr = $inputArr['category_id'];
    	$inputArr = array_except($inputArr, array('site', 'category_id'));
    	$memberObj = Member::create( $inputArr );
    	Member::saveMemberSocialIds($socialSiteArr, $memberObj->id);
        Member::saveMemberCategoryIds($categoryIdArr, $memberObj->id);


    	return Redirect::route('members.edit', [$memberObj->id])->with('message', 'Member added.');
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Member $memberObj)
	{

	    $memberSocialIdArr = Member::getMemberSocialIdArr($memberObj->id);
	    $parentChildArr = CategoryParentAndChildren::getHierarchy();
	    $categoriesArr = Category::getCategoriesArr();
	    $memberCategoryIdArr = Member::getMemberCategoryIdArr($memberObj->id);
	    
        return view('members.edit', compact('memberObj', 'memberSocialIdArr', 'parentChildArr', 'memberCategoryIdArr', 'categoriesArr'));
 
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
        $categoryIdArr = $inputArr['category_id'];
        Member::saveMemberCategoryIds($categoryIdArr, $memberObj->id);
        $inputArr = array_except($inputArr, '_method', 'site', 'category_id');
        $memberObj->update($inputArr);
        Member::saveMemberSocialIds($socialSiteArr, $memberObj->id);

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
 
	   return Redirect::route('members.index')->with('message', 'Member deleted.');
	}

}
