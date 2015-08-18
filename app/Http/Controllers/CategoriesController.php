<?php namespace App\Http\Controllers;

use App\Category;
use App\CategoryParentAndChildren;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Redirect;
use Illuminate\Http\Request;

class CategoriesController extends Controller {
    
    protected $rules = [
		'name' => ['required', 'min:3'],
		'slug' => ['required'],
		'id' => ['required']
	];
    
    public function __construct()
    {

        $this->middleware('auth');
        $this->categoryPAndCObj = new CategoryParentAndChildren();

    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Category $category)
	{

		$categoriesObj = $category->all();
        $categoriesArr = $category->getCategoriesArr($categoriesObj);
		$parentChildArr = $this->categoryPAndCObj->getHierarchy();
		return view('categories.index', compact('categoriesObj', 'parentChildArr', 'categoriesArr'));
        
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Category $category)
	{

	    $category->id = 0;
	    $parentIdNameArr = $category->getParents();
	    $selectedParentIdNameArr = $this->categoryPAndCObj->getSelectedParentIdNameArr($category->id);
	    $ddArr = $this->categoryPAndCObj->makeDDArr($parentIdNameArr, $selectedParentIdNameArr, $category->id);
        return view('categories.create', compact('category', 'ddArr', 'selectedParentIdNameArr'));
        
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  Categories $category
	 * @return Response
	 */
	public function edit(Category $category)
	{
	    $parentIdNameArr = $category->getParents();
	    $selectedParentIdNameArr = $this->categoryPAndCObj->getSelectedParentIdNameArr($category->id);
	    $ddArr = $this->categoryPAndCObj->makeDDArr($parentIdNameArr, $selectedParentIdNameArr, $category->id);
        return view('categories.edit', compact('category', 'ddArr', 'selectedParentIdNameArr'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Category $category)
	{

    	$inputArr = Input::all();
	    $parentIdArr = $inputArr['parent_id'];
	    $categoryId = $inputArr['category_id'];
    	$inputArr = array_except($inputArr, array('parent_id', 'category_id'));
    	$obj = $category->create( $inputArr );

    	$this->categoryPAndCObj->saveParentChild($parentIdArr, $obj->id, array());

    	return Redirect::route('categories.edit', [$obj->slug])->with('message', 'Category created.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  Categories $category
	 * @return Response
	 */
	public function show(Category $categoryObj)
	{
                
        $parentCatArr = $categoryObj->getParents();
	    return view('categories.show', compact('categoryObj', 'parentCatArr'));
	}
    
    public function sort(Category $categoryObj)
    {

        $inputArr = Input::all();
        $categoryObj->updateCategoryRank($inputArr['cat']);
        
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  Categories $category
	 * @return Response
	 */
	public function update(Category $category)
	{

	    $inputArr = Input::all();
	    $parentIdArr = $inputArr['parent_id'];
	    $deleteParentIdArr = isset($inputArr['delete_parent_id']) ? $inputArr['delete_parent_id'] : array();
	    $id = $inputArr['category_id'];
    	$inputArr = array_except($inputArr, array('_method', 'parent_id', 'category_id', 'delete_parent_id'));
    	$category->update($inputArr);

    	$this->categoryPAndCObj->saveParentChild($parentIdArr, $id, $deleteParentIdArr);

    	return Redirect::route('categories.edit', [$category->slug])->with('message', 'Category updated.');

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  Categories $category
	 * @return Response
	 */
	public function destroy(Category $category)
	{

        \DB::transaction(function() use($category)
        {
            $category->delete();
            $this->categoryPAndCObj->where('child_id', '=', $category->id)->orWhere('parent_id', '=', $category->id)->delete();
            \DB::table('member_categories')->where('category_id', '=', $category->id)->delete();

        });

	   return Redirect::route('categories.index')->with('message', 'Category deleted.');
	
	}

}