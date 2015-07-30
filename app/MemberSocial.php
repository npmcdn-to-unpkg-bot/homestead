<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Twitter;
use DB;
use App\Scraper;
use App\Category;
use App\SocialMedia;
//use CategoriesParentAndChildren;

class MemberSocial extends ModelNA
{
    
    public function __construct($keyword = '', $socialSite = '')
    {
        $this->socialSite = $socialSite;
        $this->keyword = $keyword;
        $this->categoryObj = new Category();
        $this->scraperObj = new Scraper();
        $this->categoriesArr = DB::table('categories')->get();
        // catPCArr category parent and child array where child_id is index and parent_id is value
        $this->catPCArr = DB::table('category_parent_and_children')->lists('parent_id', 'child_id');
    }
    
    public function getMembersAndSocialMediaWithinSingleCategory(Category $catObj)
    {
        
        // Get members within category         
        $r = DB::table('members')->select()
            ->join('member_categories', function($join) use ($catObj)
            {
                $join->on('member_categories.member_id', '=', 'members.id')
                        ->where('member_categories.category_id', '=', $catObj->id);
            })->get();

        // set member_id's into an array
        $memberIdArr = array();
        foreach($r as $obj) {
            $memberIdArr[] = $obj->member_id;
        }

        if (count($memberIdArr) ==0 ) {
            return false;
        }
        
        $q = "SELECT * FROM ";
        $q.="(";
        $q.= "SELECT * FROM social_media ";
        $q.= "WHERE member_id IN ('" . implode("',' ", $memberIdArr) . "') ";
        $q.= "ORDER BY created_at DESC";
        $q.= ") ";
        $q.= "AS tmp_table GROUP BY member_id";
        $r = DB::select($q);

        return $r;
        
    }

    public function addNewMembers(array $membersArr)
    {
        
        // checks for members not in member_social_ids table 
        // TODO problem: add twitter users and instagram users and get dups in members table
        $membersArr = $this->getMemberSocialIdsNotInDB($membersArr);
        
        foreach($membersArr as $memberSocialId => $memberObj) {
                            
            //TODO transaction 
            //TODO use exceptions
            $memberObj = $this->addMember($memberObj);
            $addCatSuccess = $this->addCategories($memberObj);
            if ($addCatSuccess == false) {
                echo "<p>scraping for: " . $memberObj->name . "</p>";
                $team = $this->scraperObj->scrapeTeam($memberObj, $this->keyword);
                if ($team) {
                    $memberObj->description = $team;
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

            // transaction commit or rollback
  
        }

    }

    
    // TODO move avatar to member_social_ids and make selectable
    // TODO make avatar updatable
    protected function addMember($memberObj)
    {

        $memberObj->id = DB::table('members')->insertGetId([
            'name' => $memberObj->name,
            'avatar' => $memberObj->avatar
        ]);
        
        DB::table('member_social_ids')
                ->where('member_social_id', '=', $memberObj->memberSocialId)
                ->where('social_site', '=', $this->socialSite)
                ->delete();
        
        DB::table('member_social_ids')->insert([
            'member_id' => $memberObj->id,
            'social_site' => $this->socialSite,
            'member_social_id' => $memberObj->memberSocialId
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
                if (strlen($word) < 3 ) {
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
    
    // get members not added to the database already    
    protected function getMemberSocialIdsNotInDB($membersArr)      
    {

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
           
    public function getMemberSocialIds()
    {
        
        return DB::table('member_social_ids')
            ->where('social_site', '=', $this->socialSite)
            ->lists('member_social_id', 'member_id');
        
    }
    
    public function getMemberIdsWithMemberSocialIds(array $memberSocialIdArr)
    {
        $arr = DB::table('member_social_ids')
            ->where('social_site', '=', $this->socialSite)
            ->whereIn('member_social_id', $memberSocialIdArr)
            ->lists('member_id', 'member_social_id');
    
        return array_change_key_case($arr);
        
    }
    
    public function addSocialMedia(array $arr) 
    {
  
        $memberSocialIdArr = array();
        foreach($arr as $key => $val) {
            $memberSocialIdArr[] = $val['memberSocialId'];
        }
        
        $memberSocialIdMemberIdArr = $this->getMemberIdsWithMemberSocialIds($memberSocialIdArr);
        
        foreach($arr as $val) {
            
            $memberId = $memberSocialIdMemberIdArr[$val['memberSocialId']];

            $socialMediaObj = new SocialMedia();            
            $socialMediaObj->setMemberSocialId($val['memberSocialId'])
                    ->setMemberId($memberId)
                    ->setSocialId($val['socialId'])
                    ->setText($val['text'])
                    ->setLink($val['link'])
                    ->setMediaUrl($val['mediaUrl'])
                    ->setMediaHeight($val['mediaHeight'])
                    ->setMediaWidth($val['mediaWidth'])
                    ->setSource($val['source']);
            
            $r = DB::table("social_media")
                ->where('member_social_id', '=', $val['memberSocialId'])
                ->where('social_id', '=', $val['socialId']);
            
            if ($r->count() ==0 ) {
                $socialMediaObj->create(get_object_vars($socialMediaObj));
            }
  
        }

    }
    
}
