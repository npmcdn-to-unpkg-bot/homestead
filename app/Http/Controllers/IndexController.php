<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Input;
use Redirect;

class IndexController extends Controller
{
    
        
    public function __construct()
    {

        //$this->middleware('auth');

    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

		return view('index.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('index.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store()
    {

        $inputArr = array_except(Input::all(), '_method');
        $id = Links::create( $inputArr );
 
        return Redirect::route('index.edit', $id)->with('message', 'Link created.');
 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
                
        $linkArr = Links::orderBy('rank', 'ASC')->lists('name','id');
	    return view('index.show', compact('linkArr'));
        
	}
    
    public function sort()
    {

        $inputArr = Input::all();
        Links::updateLinkRank($inputArr['link']);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $linkObj = Links::whereId($id)->get()->first();
        return view('index.edit', compact('linkObj'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $linkObj = Links::whereId($id)->get()->first();
        $input = array_except(Input::all(), '_method');
        $linkObj->update($input);

        return Redirect::route('index.edit', $id)->with('message', 'Link updated.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        
        Links::whereId($id)->delete();
 
        return Redirect::route('index.index')->with('message', 'Link deleted.');

    }
}
