<?php namespace App\Http\Controllers;

use App\Category;
use App\CategoryParentAndChildren;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class WelcomeController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
        $this->categoryPAndCObj = new CategoryParentAndChildren();
    }

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Category $category)
	{
 
		$categoriesObj = $category->all();
		$categoriesArr = $category->getCategoriesArr($categoriesObj);
		$parentChildArr = $this->categoryPAndCObj->getHierarchy();
		return view('welcome', compact('categoriesObj', 'parentChildArr', 'categoriesArr'));
	}

}
