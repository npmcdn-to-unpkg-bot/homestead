<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Twitter;
use DB;
use App\Scraper;
use App\Category;
use App\SocialMediaEntity;
use App\MemberSocial;

/**
 * Model for saving parsed social media feeds
 * Don't use for retrieving data and presenting to public site, only for retrieving feed data
 * and saving to db
 * There shouldn't be anything that applies only to a specific social site in this class 
 *
 * @author matt
 */
class SocialMedia extends ModelNA {

    
    /* 
     * Each instance of this class is tied to the 'keyword' and 'socialsite' passed in
     */
    public function __construct($keyword, $socialSite, $scrapeObj = false)
    {

        $this->scrapeObj = $scrapeObj;
        $this->socialSite = $socialSite;
        $this->keyword = $keyword;
        $this->memberSocialObj = new MemberSocial();
        $this->categoryObj = new Category();
        $this->categoriesArr = DB::table('categories')->get();
        // catPCArr category parent and child array where child_id is index and parent_id is value
        $this->catPCArr = DB::table('category_parent_and_children')->lists('parent_id', 'child_id');
        
    }
    
    /*
     * See 'getSimiliarSocialIds() when adding to this array
     */
    public static function getSocialSiteIdArr()
    {
        
        return array(
            'twitter' => 'Twitter',
            'instagram' => 'Instagram',
            
//            'facebook' => 'Facebook',
//            'yelp' => 'Yelp',
//            'youtube' => 'Youtube',	
//            'pinterest' => 'Pinterest',
//            'foursquare' => 'Foursquare',
        );
        
    }
    
    /*
     * Add a member (eg. twitter follower) to members table and try to categorize them
     * based on words in their description matching category words and/or scrape wikipedia page
     * TODO this should be in twitter model as the profiles of the twitter users being followed 
     * is being used to create this site's members profile data
     * TODO break out adding member_social_ids to it's own method and adding member profile to 
     * this site in another method and adding categorize member to another method
     * 
     */
    public function addNewMembers(array $membersArr, $addToMembers = false, $matchToSimiliarSocialIds = false, $categorize = false)
    {

        // set member_social_id to lowercase and set member_social_id as key in members array
        $newMembersArr = [];
        foreach($membersArr as $key => $arr) {
            $arr['member_social_id'] = strtolower($arr['member_social_id']);
            $newMembersArr[$arr['member_social_id']] = $arr;
        }
        $membersArr = $newMembersArr;

        /*// set to member_social_id to lowercase 
        array_walk($membersArr, function(&$m) {
            $m['member_social_id'] = strtolower($m['member_social_id']);
        });
         */

        // only returns members with no matching member_social_id in member_social_id table
        // members in this membersArr will have no id from member table
        $membersArr = $this->getMemberSocialIdsNotInDB($membersArr);

        $noMemberIdArr = array();
        foreach($membersArr as $key => $arr) {
 
            $memberObj = new \App\MemberEntity($arr);
         
            if ($matchToSimiliarSocialIds) {
                $memberObj->setId($this->getMemberIdWithSocialId($memberObj));
            } 

            // Add to main member table
            if ($addToMembers && $memberObj->getId() == 0) {
                // no member id for this member, insert them into the member table and assign an id
                $memberObj->insertMember();
            }
            
            // if member does not have a member id it is because a similar social id was not found
            // or $addToMembers was false, so build an array of not found so that member
            // may be added manually
            if ($memberObj->getId() ==0 ) {
                $noMemberIdArr[] = $memberObj;
                continue;
            }
            
            // reminder: getMemberSocialIdsNotInDB() allows only members without member_social_ids to be here
            $this->insertMemberSocialId($memberObj);

            if ($categorize) {
                $this->categorizeMember($memberObj);
            }

        }
        
        return $noMemberIdArr;

    }
    
    protected function categorizeMember($memberObj) 
    {

        // If they already have a category, skip
        $r = DB::table('member_categories')->select()->where('member_id', '=', $memberObj->id);
        if ($r->count() > 0) {
            return false;
        }

        $addCatSuccess = $this->addCategories($memberObj);
        if ($addCatSuccess == false  && $this->scrapeObj) {

            // TODO make scraping an overwritable method specific to subdomain's extending class
            echo "<p>scraping for: " . $memberObj->name . "</p>";
            $team = $this->scraperObj->scrapeTeam($memberObj, $this->keyword);
            if ($team) {
                echo "scrapted and found $team<br>";
                $memberObj->setDescription($team);
                $addCatSuccess = $this->addCategories($memberObj);
            }
        }

        if ($addCatSuccess) {
            echo "succeeded: " . $memberObj->name."<br>";
        } else {
            echo "<br>failed: " . $memberObj->name . "<br>"; 
            $this->insertMemberCategory($memberObj, 0);
            echo " setting as Uncategorized<br><br>";
        }

    }

    /*
     * see if they have the same username as an existing account in member_social_id table,
     * if so, use the member id associated with it 
     * if not, try and find a simliar member_social_id and use that
     */
    public function getMemberIdWithSocialId($memberObj)
    {

        // for now only do for instagram and twitter. could be made into a loop
        $memberSocialId = $memberObj->getMemberSocialId();
        foreach(self::getSocialSiteIdArr() as $socialSiteKey => $socialSiteName) {
            
            // Get member id for SAME social id but different from source member came from
            if ($socialSiteKey != $memberObj->getSource()) {
                // eg. if 'twitter' != 'instagram'
                // eg. if current member is from instagram, look for same memberSocialId from twitter
                $arr = $this->memberSocialObj->getMemberIdsWithMemberSocialIds(array($memberSocialId), $socialSiteKey);
                if (count($arr) == 1) {
                    return array_shift($arr);
                }
                
                // Get member id with SIMILAR social id
                $memberId = $this->getMemberIdWithSimilarSocialId($memberSocialId, $socialSiteKey);
                if ($memberId) {
                    return $memberId;
                }                
                
            }

        }

        return 0;

    }
    
    /*
     * In order to link up the same person on different social sites, try and match the social member id
     * without _ (underscore) or numbers
     * eg. twitter screenname is wanda_june123 and instagrame username is wandajune. Since both were selected 
     * to be followed on both social sites, it is safe to say they're the same person
     * NOTE: these queries make use of 'social_site' column, so if it's an instagram user,
     * you may want to pass in 'twitter' to looke for similar social_id's associated with a twitter account
     */
    protected function getMemberIdWithSimilarSocialId($memberSocialId, $socialSite)
    {
        
        $memberSocialIdNoNumbers = preg_replace("~[0-9]~", "", $memberSocialId);
        $memberSocialIdNoUnderscore = str_replace("_", "", $memberSocialId);
        $memberSocialIdLettersOnly = preg_replace("~[0-9_]~", "", $memberSocialId);
        $endPos = strlen($memberSocialId);
        if ($endPos >= 12) {
            $endPos = $endPos - 4; 
        }
        $memberSocialIdShort = substr($memberSocialId, 0, $endPos);
        
        $q = "SELECT member_id,member_social_id ";
        $q.= "FROM member_social_ids ";
        $q.= "WHERE social_site = '" . $socialSite . "' ";
        $q.= "AND (";
        $q.= "member_social_id = '" . $memberSocialIdNoNumbers . "' ";
        $q.= "OR ";
        $q.= "REPLACE(member_social_id, '_', '') = '" . $memberSocialIdNoUnderscore . "' ";
        $q.= "OR ";
        $q.= "member_social_id = '" . $memberSocialIdLettersOnly . "' ";
        $q.= ") ";
        $q.= "GROUP BY member_id";
        $r = DB::select($q);
        
        if (count($r) > 0) {
            echo "<p>matched: ". $q . "</p>";
            return $r[0]->member_id;
        }
        
        $q = "SELECT member_id,member_social_id ";
        $q.= "FROM member_social_ids ";
        $q.= "WHERE social_site = '" . $socialSite . "' ";
        $q.= "AND ";
        $q.= "member_social_id LIKE '" . $memberSocialIdShort . "%' ";
        $q.= "GROUP BY member_id";
        $r = DB::select($q);
        
        if (count($r) >0 ) {
            echo "<p>matched: ". $q . "</p>";
            return $r[0]->member_id;
        }
        
        $q = "SELECT name, id as member_id ";
        $q.= "FROM members ";
        $q.= "WHERE REPLACE(name, ' ', '') LIKE '" . $memberSocialIdShort . "%' ";
        $q.= "GROUP BY member_id";
        $r = DB::select($q);
        
        if (count($r) > 0) {
            echo "<p>matched: ". $q . "</p>";
            return $r[0]->member_id;
        }
        
        return 0;

    }
    
    // TODO move avatar to member_social_ids and make selectable
    // TODO make avatar updatable
    protected function insertMemberSocialId($memberObj)
    {
/*
        $r = DB::table('member_social_ids')
                ->where('member_social_id', '=', $memberObj->getMemberSocialId())
                ->where('social_site', '=', $this->socialSite)
                ->get();
*/
        DB::table('member_social_ids')->insert([
            'member_id' => $memberObj->id,
            'social_site' => $this->socialSite,
            'member_social_id' => $memberObj->getMemberSocialId()
        ]);
        
    }
    
    /*
     * Try and add them to a category
     * 
     */
    protected function addCategories($memberObj)
    {
        
        $categoriesArr = $this->categoriesArr;
        /* eg. Array(
            [0] => stdClass Object
                (
                    [id] => 18
                    [name] => Atlantic
                    [display_name] => Atlantic
                    [slug] => atlantic
                    [written_at] => 2015-07-26 15:42:55
                )
        */

        // child_id's pointing to their parent_id's
        $catPCArr = $this->catPCArr;
        /* eg.  Array(
            [18] => 0
            [19] => 0
            ...
            [49] => 18
            ...
            [44] => 19
            [45] => 19
        */

        // look for the category in the description
        $catSetArr = array();
        $childIdFound = false;
        $parentIdFound = false;
        foreach($this->categoriesArr as $catObj) {
            
            $memberObj = $this->setChildParentIds($catObj->display_name, $memberObj, $catObj);
                       
            // If we have the childId, we have the parentId and can stop looking
            if ($memberObj->childId >0 ) {
                $this->saveChildParentIds($memberObj);
                break;
            }
         
        }
        
        if ($memberObj->childId >0 ) {
            return true;
        }
        
        
        // break up category name, if possible, and search for each
        // word greater than 3 characters in the member description
        foreach($this->categoriesArr as $catObj) {
            
            $arr = explode(" ", $catObj->display_name);
            if (count($arr) == 1 ) {
                continue;
            }
            
            // reverse array since most significant part is last eg. Los Angeles Lakers, 
            // Lakers is most significant part and will avoid conflicts with Los Angeles Clippers
            $arr = array_reverse($arr);
            
            foreach($arr as $word) {
                
                $word = trim($word);
                if (strlen($word) < 4 ) {
                    continue;
                }
                
                $memberObj = $this->setChildParentIds($word, $memberObj, $catObj);
                // If we have the childId, we have the parentId and can stop looking
                if ($memberObj->childId >0 ) {
                    break;
                }
                
            }
        }

        if ($memberObj->childId >0 || $memberObj->parentId >0 ) {
            $this->saveChildParentIds($memberObj);
            return true;
        }
        
        return false;
        
    }
    
    protected function saveChildParentIds($memberObj) 
    {
        if ($memberObj->childId >0 ) {
            $this->insertMemberCategory($memberObj, $memberObj->childId);
        }
        if ($memberObj->parentId >0 ) {
            $this->insertMemberCategory($memberObj, $memberObj->parentId);
        }
    }
    
    protected function setChildParentIds($text, $memberObj, $catObj)
    {
    
        // eg. if 'Los Angeles Lakers' text is in description
        if (stristr($memberObj->description, $text)) {
            if ($this->isChildId($catObj)) {
                $memberObj->childId = $catObj->id;
                $memberObj->parentId = $this->catPCArr[$memberObj->childId];
            } else if ($this->isParentId($catObj)) {
                $memberObj->parentId = $catObj->id;
            } 
            
        }
        
        return $memberObj;
        
    }

    /*
     * $this->catPCArr is a lookup array with childId as index and parentId as value
     * Array values of 0 given an index value means the given index value is a parent
     */
    private function isParentId($catObj) 
    {
        return (isset($this->catPCArr[$catObj->id]) && $this->catPCArr[$catObj->id] == 0 ) ? true : false;
    }
    
    /* $this->catPCArr is a lookup array with childId as index and parentId as value
     * Array values greater than zero given and index means the given index value has a parent and is thus a child 
     */
    private function isChildId($catObj)
    {
        return (isset($this->catPCArr[$catObj->id]) && $this->catPCArr[$catObj->id] > 0 ) ? true : false;        
    }
        
    private function insertMemberCategory($memberObj, $catId)
    {
        
        $r = DB::table('member_categories')->select()
                ->where('member_id', '=', $memberObj->id)
                ->where('category_id', '=', $catId);

        if ($r->count() == 0) {
            DB::table('member_categories')->insert([
                'member_id' => $memberObj->id,
                'category_id' => $catId
            ]);
            // delete any 'uncategorized' row
            if ($catId >0 ) {
                DB::table('member_categories')
                    ->where('member_id', '=', $memberObj->id)
                    ->where('category_id', '=', '0')
                    ->delete();
            }
        }
    }
    
    // get members whose social id (twitter screename or whichever site) has not added to the database already    
    public function getMemberSocialIdsNotInDB($membersArr)      
    {

        //$memberSocialIdArr = array_map(function ($arr) {return $arr['member_social_id'];}, $membersArr);

        $memberSocialIdDBArr = DB::table('member_social_ids')
            ->whereIn('member_social_id', array_keys($membersArr))
            ->where('social_site', '=', $this->socialSite)
            ->select('member_social_id')
            ->get();

        foreach($memberSocialIdDBArr as $key => $memberSocialIdDB) {
            unset($membersArr[strtolower($memberSocialIdDB->member_social_id)]);
        }
        
        if (is_null($membersArr)) {
            return array();
        }
        
        return $membersArr;

    }
    
    public function addSocialMedia(array $socialMediaArr) 
    {
  
        $memberSocialIdArr = array();
        foreach($socialMediaArr as $key => $val) {
            $memberSocialIdArr[] = $val['memberSocialId'];
        }

        $memberSocialIdMemberIdArr = $this->memberSocialObj->getMemberIdsWithMemberSocialIds($memberSocialIdArr, $this->socialSite);

        // sort by time so that social_media.id generated by db insert will be in order with date
        usort($socialMediaArr, array($this, 'sortByWrittenAt'));

        foreach($socialMediaArr as $val) {
            
            // addfriend may not have been called
            if (!isset($memberSocialIdMemberIdArr[$val['memberSocialId']])) {
                continue;
            }
            
            $memberId = $memberSocialIdMemberIdArr[$val['memberSocialId']];

            $socialMediaEnt = new SocialMediaEntity();            
            $socialMediaEnt->setMemberSocialId($val['memberSocialId'])
                    ->setMemberId($memberId)
                    ->setSocialId($val['socialId'])
                    ->setText($val['text'])
                    ->setLink($val['link'])
                    ->setMediaUrl($val['mediaUrl'])
                    ->setMediaHeight($val['mediaHeight'])
                    ->setMediaWidth($val['mediaWidth'])
                    ->setWrittenAt($val['written_at'])
                    ->setSource($val['source']);
    
            $r = DB::table("social_media")
                ->where('member_social_id', '=', $val['memberSocialId'])
                ->where('social_id', '=', $val['socialId']);
            
            if ($r->count() ==0 ) {
                echo '<br>adding ' . $socialMediaEnt->member_social_id . ' status for id ';
                echo $socialMediaEnt->social_id.'<br>';
                $arr = get_object_vars($socialMediaEnt);
                //printR($arr);
                $socialMediaEnt->create($arr);
            } else {
                echo '<br>already added ';
                echo $socialMediaEnt->member_social_id . ' status for id ' . $socialMediaEnt->social_id.'<br>';
            }
  
        }

    }    
    
}
