<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\TwitterAdapter;
use App\Scraper;
use App\SocialMedia;
use App\Site;

class TwitterController extends Controller
{
    
    public function __construct()
    {
        
        $keyword = Site::getInstance()->getSubdomain();
        $twitterScreenName = Site::getInstance()->getTwitterScreenName();

        $scraperObj = false;
        if ($keyword == 'nba') {
            $scraperObj = new Scraper($keyword);
        }
        $this->socialMediaObj = new SocialMedia($keyword, 'twitter', $scraperObj);
        $this->twitterAdapter = new TwitterAdapter($twitterScreenName);
        
    }
    
    public function getFeed()
    {
        
        if (($socialMediaArr = $this->twitterAdapter->getFeed()) !== false) {
            $this->socialMediaObj->addSocialMedia($socialMediaArr);
        }
        printR($socialMediaArr);
        exit('done');
    }
    
    public function getFriends()
    {

        $cursor = -1;
        do {
            $cursor = $this->twitterAdapter->getFriends($cursor);
        } while($cursor > 0);
        
        // operate on the formatted twitter feed
        $friendsArr = $this->twitterAdapter->getFriendsArr();

        if (count($friendsArr) >0 ) {

            $addToMembersTable = true; 
            $matchToSimiliarSocialIds = false; // use twitter account as main source of ids
            $categorize = true;
            $this->socialMediaObj->addNewMembers($friendsArr, $addToMembersTable, $matchToSimiliarSocialIds, $categorize);
            
        } else {
            echo "No new Twitter followers to add.";
        }
        printR($friendsArr);
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
