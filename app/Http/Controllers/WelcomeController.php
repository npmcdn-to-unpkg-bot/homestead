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
		//$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{

		$categoriesObj = Category::all();
		$categoriesArr = Category::getCategoriesArr($categoriesObj);
		$parentChildArr = CategoryParentAndChildren::getHierarchy();
		return view('welcome', compact('categoriesObj', 'parentChildArr', 'categoriesArr'));
	}

}
