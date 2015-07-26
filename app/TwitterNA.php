<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Twitter;
use DB;
use Category;

class TwitterNA extends Model
{

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
    public function addNewMembers()
    {

        // get members being followed on twitter
        $paramArr = ['screen_name' => 'nbablvd', 'skip_status' => true, 'include_user_entities' => false];
        
        if (0) { 
            $r = Twitter::getFriends($paramArr); 
        } else {
            $r = new \stdClass();
            $mem = new \stdClass();
            $mem->screen_name = 'Adrien_Payne';
            $mem->name = 'Adreian Payne';
            $mem->description = 'Blah blah minnesota Timberwolves';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/3439961848/64f4a9275de184af5e381486492000b2_normal.jpeg';
            $r->users[0] = $mem; 
            $mem = new \stdClass();
            $mem->screen_name = 'Kobe_Bryant';
            $mem->name = 'Kobe Bryan';
            $mem->description = 'Blah blah LAKERS ASDF';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/3439961848/64f4a9275de184af5e381486492000b2_normal.jpeg';
            $r->users[1] = $mem; 
        }
        //printR($r);exit;
        $screenNameArr = $this->retrieveScreenNameArr($r->users);
        if ($screenNameArr === false) { 
            return false;
        }
        
        $screenNameArr = $this->getScreenNamesNotInDB($screenNameArr);
        
        foreach($screenNameArr as $screenName) {
            $memberObj = $this->getMemberDetails($screenName, $r->users);
            if ($memberObj) {
                // transaction 
                $memberObj = $this->addMember($memberObj);
                $this->addCategories($memberObj);
                // transaction commit or rollback
            }    
        }

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
    
    protected function addMember($memberObj)
    {

        $memberObj->member_id = DB::table('members')->insertGetId([
            'first_name' => $memberObj->firstName,
            'last_name' => $memberObj->lastName,
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
        
        if (is_null($this->categoriesArr)) {
            $this->categoriesArr = DB::table('categories')->get();
            if (count($this->categoriesArr) == 0) {
                return false;
            }
        }
        
        // look for the category in the description
        $found = false;
        foreach($this->categoriesArr as $obj) {
            if (stristr($memberObj->description, $obj->display_name)) {
                $found = true;
                $this->insertMemberCategory($memberObj, $obj->id);
                break;
            }
        }
        
        // if not found, break up category name, if possible, and search for each
        // word greater than 3 characters in the member description
        if ($found === false) {
            foreach($this->categoriesArr as $obj) {
                $arr = explode(" ", $obj->display_name);
                if (count($arr) == 1 ) {
                    continue;
                }
                foreach($arr as $word) {
                    if (strlen(trim($word)) < 3 ) {
                        continue;
                    }
                    if (stristr($memberObj->description, trim($word))) {
                        $this->insertMemberCategory($memberObj, $obj->id);
                        break;
                    }
                }
            }   
        }
        
    }
    
    private function insertMemberCategory($memberObj, $catId)
    {
        DB::table('member_categories')->insert([
            'member_id' => $memberObj->member_id,
            'category_id' => $catId
        ]);  
    }
    
    protected function getMemberDetails($screenName, $usersArr) 
    {
        
        $memberObj = false;
        foreach($usersArr as $obj) {
            if (strtolower($screenName) == strtolower($obj->screen_name)) {
                $memberObj = $obj;
                $pos = strpos($memberObj->name, " ");
                $memberObj->firstName = trim(substr($memberObj->name, 0, $pos));
                $memberObj->lastName = trim(substr($memberObj->name, $pos));
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
        
        return $screenNameArr;

    }
           
    

    
   
    
    
    
}
