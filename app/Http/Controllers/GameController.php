<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Input;
use Redirect;

class GameController extends Controller
{


    public function __construct()
    {

        //$this->middleware('auth');

    }

    public function jquerymatch()
    {
        return view('game.jquerymatch');
    }

    public function reactmatch()
    {

        return view('game.reactmatch');
    }
    public function react()
    {

        return view('game.react');
    }
    public function searchfilter()
    {

        return view('game.searchfilter');
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

        //$linkArr = Links::orderBy('rank', 'ASC')->lists('name','id');
        //return view('index.show', compact('linkArr'));

    }

    public function sort()
    {

        //$inputArr = Input::all();
        //Links::updateLinkRank($inputArr['link']);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //$linkObj = Links::whereId($id)->get()->first();
        //return view('index.edit', compact('linkObj'));
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
//        $linkObj = Links::whereId($id)->get()->first();
//        $input = array_except(Input::all(), '_method');
//        $linkObj->update($input);
//
//        return Redirect::route('index.edit', $id)->with('message', 'Link updated.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

//        Links::whereId($id)->delete();
//
//        return Redirect::route('index.index')->with('message', 'Link deleted.');

    }
}
