<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Twitter;
use DB;
use App\Scraper;
use App\Category;
//use CategoriesParentAndChildren;

class TwitterNA extends ModelNA
{
    
    public function __construct()
    {

        $this->categoryObj = new Category();
        $this->scraperObj = new Scraper();
        $this->categoriesArr = DB::table('categories')->get();
        // catPCArr category parent and child array where child_id is index and parent_id is value
        $this->catPCArr = DB::table('category_parent_and_children')->lists('parent_id', 'child_id');
    }

    private function getFriendsIds($screenName = 'nbablvd', $count = 2, $format = 'json')
    {

        $paramArr = ['screen_name' => $screenName, 'count' => $count, 'format' => $format, 'stringify_ids' => 1];
        $r = Twitter::getFriendsIds($paramArr);
        $obj = json_decode($r);
        return $obj->ids;
        
    }

    //private function setParams($paramArr, 'skip_status' => 1,)
    	/**
	 * Returns a cursored collection of user objects for every user the specified user is following (otherwise known as their “friends”).
	 *
	 * Parameters :
	 * - user_id
	 * - screen_name
	 * - cursor
	 * - skip_status (0|1)
	 * - include_user_entities (0|1)
	 */
    public function addNewMembers($nextCursor = -1)
    {

        // get members being followed on twitter
        $paramArr = ['screen_name' => 'nbablvd', 'skip_status' => true, 'include_user_entities' => false, 'cursor' => $nextCursor];
        
        if (1) { 
            $r = Twitter::getFriends($paramArr); 
        } else {
            $r = new \stdClass();
            $mem = new \stdClass();
            $mem->screen_name = 'Adrien_Payne';
            $mem->name = 'Adreian Payne';
            $mem->description = 'Blah blah northwest minnesota Timberwolves';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/3439961848/64f4a9275de184af5e381486492000b2_normal.jpeg';
            $r->users[2] = $mem; 
            $mem = new \stdClass();
            $mem->screen_name = 'Kobe_Bryant';
            $mem->name = 'Kobe Bryan';
            $mem->description = 'Blah blah LAKERS ASDF';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/3439961848/64f4a9275de184af5e381486492000b2_normal.jpeg';
            $r->users[1] = $mem; 
            $mem = new \stdClass();
            $mem->name = 'Jason Thompson';
            $mem->screen_name = 'jtthekid';
            $mem->description = 'PF/C for Philadelphia 76ers. iG: Jtthekid Manager: @dschwartze1 Mr. 519+';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/618625788460560384/iwS9dPYE_normal.jpg';
            $r->users[0] = $mem; 
            $r->next_cursor_string = -1;
        }
        
        if (!isset($r->users)) {
            exit ('users is not a property of twitter result');
        }
        
        //printR($r);
        //exit;
        $screenNameArr = $this->retrieveScreenNameArr($r->users);
        if ($screenNameArr === false) { 
            return $r->next_cursor_string;
        }
        
        $screenNameArr = $this->getScreenNamesNotInDB($screenNameArr);
        
        foreach($screenNameArr as $screenName) {

            $memberObj = $this->getMemberDetails($screenName, $r->users);
            if ($memberObj) {
                //TODO transaction 
                $memberObj = $this->addMember($memberObj);
                $addCatSuccess = $this->addCategories($memberObj);
                if ($addCatSuccess == false) {
                    $team = $this->scraperObj->scrapeTeam($memberObj, 'nba');
                    $memberObj->description = $team;
                }
                
                $addCatSuccess = $this->addCategories($memberObj);
                if ($addCatSuccess == false){
                    echo "<br>failed: " . $memberObj->name . "<br>"; 
                    if ($id = $this->categoryObj->getCategoryIdWithName('Free Agent') == true) {
                        $this->insertMemberCategory($memberObj, $id);
                        echo " setting as Free Agent<br><br>";
                    }
                } else {
                    echo "<br>succeeded: " . $memberObj->name."<br>";
                }
                
                // transaction commit or rollback
            } else {
                echo "memberObj not set via getMemberDetails() <br>";
            }    
        }
        
        return $r->next_cursor_str;

    }
        
       /*
     * Extract screen names from twitter friends list and return as an array
     * $twitterFriendArr is in the format:
     * stdClass Object
    stdClass Object
    (
        [users] => Array
            (
                [0] => stdClass Object
                    (
                        [id] => 380051568
                        [id_str] => 380051568
                        [name] => Adreian Payne
                        [screen_name] => Adreian_Payne
                        [description] => |A MAN on..
                        [profile_image_url] => http://pbs.twimg.com/profile_images/3439961848/64f4a9275de184af5e381486492000b2_normal.jpeg
                        etc.
     * 
     */
    protected function retrieveScreenNameArr($twitterFriendArr)
    {
        
        $screenNameArr = array();
        foreach($twitterFriendArr as $obj) {
            $screenNameArr[strtolower($obj->screen_name)] = $obj->screen_name;
        }
        
        if (count($screenNameArr) == 0 ) {
            return false;
        }
        
        return $screenNameArr;
        
    }
    
    // TODO move avatar to member_social_ids and make selectable
    // TODO make avatar updatable
    protected function addMember($memberObj)
    {

        $memberObj->member_id = DB::table('members')->insertGetId([
            'name' => $memberObj->name,
            'avatar' => $memberObj->profile_image_url
        ]);
        
        DB::table('member_social_ids')
                ->where('member_social_id', '=', $memberObj->screen_name)
                ->where('social_site', '=', 'twitter')
                ->delete();
        
        DB::table('member_social_ids')->insert([
            'member_id' => $memberObj->member_id,
            'social_site' => 'twitter',
            'member_social_id' => $memberObj->screen_name
        ]);
        
        return $memberObj;
        
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
                    [created_at] => 2015-07-26 15:42:55
                    [updated_at] => 2015-07-26 15:42:55
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
        foreach($this->categoriesArr as $obj) {
            
            // non-parent example A: description 'Hello Los Angeles Lakers fans!', display_name 'Los Angeles Lakers'
            // parent example B: description 'Great to be playing on the Pacific Coast!' display_name 'Pacific'
            if (stristr($memberObj->description, $obj->display_name) && !isset($catSetArr[$obj->id])) {
                
                $catSetArr[$obj->id] = 1;
                
                // Try to set child_id first, and from that, the parent_id
                // example A goes into this method and 'Los Angeles Lakers' and its parent 'Pacific' is set
                list($childIdFound, $parentIdFound) = $this->saveParentAndChild($parentIdFound, $childIdFound, $obj, $memberObj);
                
                // If the child/parent was not set above, see if parent alone can be set
                // example A skips this conditional
                // example B goes into this conditional and parentIdFound is set
                if ($this->isParentId($obj) && $parentIdFound === false) {
                    $parentIdFound = $obj->id;
                    $this->insertMemberCategory($memberObj, $parentIdFound);
                }
                    
                if ($parentIdFound && $childIdFound) {
                    break;
                }
            }
        }
        
        if ($parentIdFound && $childIdFound) {
            return true;
        }
        
        // break up category name, if possible, and search for each
        // word greater than 3 characters in the member description
        foreach($this->categoriesArr as $obj) {
            
            $arr = explode(" ", $obj->display_name);
            if (count($arr) == 1 ) {
                continue;
            }
            
            // reverse array since most significant part is last eg. Los Angeles Lakers, 
            // Lakers is most significant part and will avoid conflicts with Los Angeles Clippers
            $arr = array_reverse($arr);
            
            foreach($arr as $word) {
                
                if (strlen(trim($word)) < 3 ) {
                    continue;
                }
                
                if (stristr($memberObj->description, trim($word)) && !isset($catSetArr[$obj->id])) {
                    
                    $catSetArr[$obj->id] = 1;
                    
                    list($childIdFound, $parentIdFound) = $this->saveParentAndChild($parentIdFound, $childIdFound, $obj, $memberObj);

                    // the isset($catSetArr) check above prevents duplicates here
                    if ($this->isParentId($obj) == true && $parentIdFound === false) {
                        $parentIdFound = $this->catPCArr[$obj->id];
                        $this->insertMemberCategory($memberObj, $parentIdFound);
                    } 

                    if ($childIdFound && $parentIdFound) {
                        return true;
                    }
                }
                
            }
        }
        
        return false;
        
    }
    
    // if the childId has not been found and the current category id is not a top level parent category
    // (eg. top level parent category 'Pacific' for nba) 
    protected function saveParentAndChild($parentIdFound, $childIdFound, $catObj, $memberObj)
    {
        
        if ($this->isParentId($catObj) == false && $childIdFound === false) {
            $childIdFound = $catObj->id;
            $this->insertMemberCategory($memberObj, $childIdFound);
            if ($parentIdFound === false) {
                $parentIdFound = $this->catPCArr[$childIdFound];
                $this->insertMemberCategory($memberObj, $parentIdFound);
            }
            
        }
        
        return array($childIdFound, $parentIdFound);
        
    }
    
    private function isParentId($catObj) 
    {
        return (isset($this->catPCArr[$catObj->id]) && $this->catPCArr[$catObj->id] == 0 ) ? true : false;
    }
        
    private function insertMemberCategory($memberObj, $catId)
    {
        $r = DB::table('member_categories')->select()
                ->where('member_id', '=', $memberObj->member_id)
                ->where('category_id', '=', $catId);

        if (count($r) == 0) {
            DB::table('member_categories')->insert([
                'member_id' => $memberObj->member_id,
                'category_id' => $catId
            ]);  
        }
    }
    
    protected function getMemberDetails($screenName, $usersArr) 
    {

        foreach($usersArr as $obj) {
            $memberObj = false;
            if (strtolower($screenName) == strtolower($obj->screen_name)) {
                $memberObj = $obj;
                /*
                $pos = strpos($memberObj->name, " ");
                $memberObj->firstName = trim(substr($memberObj->name, 0, $pos));
                $memberObj->lastName = trim(substr($memberObj->name, $pos));
                 * */
                break;
            }
        }
        
        return $memberObj;
        
    }
    
    // get members not added to the database already    
    protected function getScreenNamesNotInDB($screenNameArr)      
    {

        $screenNameDBArr = DB::table('member_social_ids')
            ->whereIn('member_social_id', $screenNameArr)
            ->where('social_site', '=', 'twitter')
            ->select('member_social_id')
            ->get();

        foreach($screenNameDBArr as $key => $screenNameDB) {
            unset($screenNameArr[strtolower($screenNameDB->member_social_id)]);
        }
        
        if (is_null($screenNameArr)) {
            return array();
        }
        
        return $screenNameArr;

    }
           
    

    
   
    
    
    
}
