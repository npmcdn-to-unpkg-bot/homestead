<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\TwitterAdapter;
use App\Scraper;
use App\SocialMedia;
use App\Site;

/**
 * This method is called by a cron job and any and all output will not returned to a view, but
 * to whatever the cron job is set to do. No presentation of output other than basic formatting 
 * is necessary
 */
class TwitterController extends Controller
{
    
    /**
     * Initialize objects that will do the work
     */
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
    
    /**
     * Retrieve the tweets of people being followed by 'twitterScreenName'
     * 
     */
    public function getFeed()
    {
        
        if (($socialMediaArr = $this->twitterAdapter->getFeed()) !== false) {
            $this->socialMediaObj->addSocialMedia($socialMediaArr);
        }
        printR($socialMediaArr);
        exit('done');
    }
    
    /**
     * Get the friends that $twitterScreenName is following 
     * 
     */
    public function getFriends()
    {

        $cursor = -1;
        do {
            $cursor = $this->twitterAdapter->getFriends($cursor);
        } while($cursor > 0);
        
        $friendsArr = $this->twitterAdapter->getFriendsArr();
        if (count($friendsArr) >0 ) {

            $addToMembersTable = true; 
            $matchToSimiliarSocialIds = false; 
            $categorize = true;
            $this->socialMediaObj->addNewMembers($friendsArr, $addToMembersTable, $matchToSimiliarSocialIds, $categorize);
            
        } else {
            echo "No new Twitter followers to add.";
        }
        printR($friendsArr);
        exit('asfd');
    }

}
