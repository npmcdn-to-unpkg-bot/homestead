<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\TwitterNA;

class TwitterController extends Controller
{
    
    public function __construct()
    {
        $this->twitterNA = new TwitterNA();
    }
    
    public function addNewMembers()
    {
        Twitter::addNewMembers();
        exit('asfd');
    }
    
    public function getFriendsIds()
    {

        $r = Twitter::getFriendsIds(['screen_name' => 'nbablvd', 'count' => 20, 'format' => 'json']);
        $obj = json_decode($r);
        $idArr = $obj->ids;
        $r = Twitter::getFriends($idArr);
        print_r($r);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->twitterNA->addNewMembers();
        exit('asfd');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
