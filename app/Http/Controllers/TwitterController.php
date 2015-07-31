<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\TwitterAdapter;
use App\MemberSocial;
use App\SocialMedia;

class TwitterController extends Controller
{
    
    public function __construct()
    {
        // TODO set these objects and their params via route and pass in
        $this->socialMediaObj = new SocialMedia('nba', 'twitter');
        $this->twitterAdapter = new TwitterAdapter('nbablvd');
    }
    
    public function addStatus()
    {
        
        if (($socialMediaArr = $this->twitterAdapter->addStatus()) !== false) {
            $this->socialMediaObj->addSocialMedia($socialMediaArr);
        }
        printR($socialMediaArr);
        exit('done');
    }
    
    public function addFriends()
    {

        $cursor = -1;
        do {
            $cursor = $this->twitterAdapter->parseMembers($cursor);
        } while($cursor > 0);
        
        // operate on the formatted twitter feed
        if (count($this->twitterAdapter->getMemberArr()) >0 ) {
            $this->socialMediaObj->addNewMembers($this->twitterAdapter->getMemberArr());
        }
        
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
    
    public function getFriendsIds()
    {

        $r = Twitter::getFriendsIds(['screen_name' => 'nbablvd', 'count' => 20, 'format' => 'json']);
        $obj = json_decode($r);
        $idArr = $obj->ids;
        $r = Twitter::getFriends($idArr);
        print_r($r);
    }    
}
