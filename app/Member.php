<?php namespace App;

use App\ModelNA;
//use Illuminate\Database\Eloquent\Model;
use DB;

use App\CategoryParentAndChildren;
use App\Category;
use App\SocialMedia;

class Member extends ModelNA {
            
    protected $fillable = array('name', 'slug', 'avatar');
    
        
    /*
     * Get members within a category and order members from most recent social media post to oldest
     */
    public function getMembersWithinSingleCategory($catId)
    {
     
        $r = \Cache::get($catId);
        if ($r === null) {
            $q = "SELECT * FROM (
            SELECT members.id, members.name, avatar, social_media.written_at  
            FROM members ";
            $q.= "INNER JOIN member_categories ON members.id = member_categories.member_id AND category_id = " . intval($catId) . " 
            LEFT JOIN member_social_ids AS msi ON msi.member_id = members.id AND msi.primary_avatar = 1 AND msi.disabled = 0 
            LEFT JOIN social_media ON (social_media.member_id = members.id) 
            ORDER BY social_media.written_at DESC 
            ) AS tmp_table 
            GROUP BY tmp_table.id 
            ORDER BY tmp_table.written_at DESC";
            $r = DB::select($q); 
            \Cache::put($catId, $r, 60);
        }
        
        return $r;
        
    }
    
    /////////////////////////////////////
    // Begin instagram location id
    //
    /**
     * Get instagram location id for member
     * 
     * @param array $memberArr multi-dim array with unique id 
     * 
     * @return array
     */
    public function getInstagramLocationIds(array $memberArr)
    {
        if (count($memberArr) == 0) {
            return array();
        } 
        
        $memberIdArr = array_map(function ($arr) {return $arr['id'];}, $memberArr);

        $q = "SELECT * FROM instagram_location_ids WHERE member_id IN (" . implode(", ", $memberIdArr) . ")";
        $r = DB::select($q);

        return $r;
        
    }
    
    /**
     * Save instagram location id
     * 
     * @param int $memberId
     * 
     * @param int $locationId
     */
    public function saveInstagramLocationId($memberId, $locationId) 
    {
        
        $q = "DELETE FROM instagram_location_ids WHERE member_id = " . intval($memberId);
        DB::statement($q);
        
        if ($locationId != 0) {
            \DB::table('instagram_location_ids')->insert(
                [
                'member_id' => $memberId,
                'location_id' => $locationId
                ]
            );
        }

    }
    
    /**
     * Get single location id
     * 
     * @param int id of member 
     * 
     * @return int
     */
    public function getInstagramLocationId($memberId)
    {
        
        $memberArr = array(array('id' => $memberId));
        $r = $this->getInstagramLocationIds($memberArr);
        if (isset($r[0]->location_id)) {
            return $r[0]->location_id;
        }
        return '';
        
    }
    //
    // End instagram location id
    /////////////////////////////////////////////

    /*
     * Get category_ids member belongs to
     * 
     * return
     */
    public function getMemberCategoryIdArr($memberId)
    {
        
        $memberCategoryIdArr = DB::table('member_categories')->where('member_id', $memberId)->lists('category_id');
        return $memberCategoryIdArr;
        
    }

    public  function saveMemberCategoryIds($categoryIdArr, $memberId)
    {
        
        DB::transaction(function() use($memberId, $categoryIdArr)
        {
            
            // delete existing categories in table
            DB::table('member_categories')->where('member_id', $memberId)->delete(); 

            $valuesArr = array();
            foreach($categoryIdArr as $categoryId) {
               $valuesArr[] = array('member_id' => $memberId, 'category_id' => $categoryId );
            }

            if (count($valuesArr) > 0) {
               DB::table('member_categories')->insert($valuesArr);
            }
        
        });
        
        
    }
    
    /*
     * Get members that have a parent, but no child
     */
    public function getNoChild($next = 0, $limit = 15)
    {
        
        $r = $this->select('members.*', DB::raw('count(*) as num'))
            ->join('member_categories', function($join)
            {
                $join->on('member_categories.member_id', '=', 'members.id')
                        ->where('member_categories.category_id', '!=', 0);
            })               
            ->groupBy('member_categories.member_id')
            ->havingRaw('num < 2')
            ->skip($next)->take($limit)
            ->get();

        return $r;
        
    }
    
    public function getNoCategory($next = 0, $limit = 15)
    {
        
        $r = $this->select('members.*')
            ->join('member_categories as mc', 'mc.member_id', '=', 'members.id')
            ->where('mc.category_id', '=', 0)
            ->skip($next)->take($limit)
            ->get();
        return $r;                  
        
    }
    
    /*
     * Get members that belong to a category
     */
    public  function getMembersWithSlugSimple($slug, $next = 0, $limit = 15)
    {

        // TODO optimize - where clause with 'slug = $slug' should come first to greatly reduce
        // rows scanned
        $r = $this->select('members.*','categories.display_name') 
            ->join('member_categories', 'members.id', '=', 'member_categories.member_id')
            ->join('categories', function($join) use ($slug)
            {
                $join->on('categories.id', '=', 'member_categories.category_id')
                        ->where('categories.slug', '=', $slug);
            })
            ->skip($next)->take($limit);

        return $r->get();

        
    }


    /*
     * Create an array of social sites and the ids a member has for each one
     * 
     * returns array in format:
     * ['twitter'] = array('name' => 'Twitter', 'memberSocialId' => '3r9230rj23rj', 'disabled' => 1),
     * ['instagram'] = array('name' => etc
     */
    public function getMemberSocialIdArr($memberId)
    {
        
        // get member's social ids (eg. twitter screen_name 'nba_playa')
        $memberSocialIdArr = DB::table('member_social_ids')
                ->where('member_id', '=', $memberId)
                ->get();

        // get array of social sites and set member's social ids for each site
        $socialIdSiteArr = SocialMedia::getSocialSiteIdArr();
        $fullMemberSocialIdArr = array();
        foreach($socialIdSiteArr as $socialId => $socialSite) {
            $memberSocialId = '';
            $disabled = 1;
            $avatar = '';
            $primaryAvatar = 0;
            foreach($memberSocialIdArr as $key => $obj) {
                if ($obj->social_site == $socialId) {
                    $memberSocialId = $obj->member_social_id;
                    $disabled = $obj->disabled;
                    $avatar = $obj->avatar;
                    $primaryAvatar = $obj->primary_avatar;
                    break;
                }
            }
            $fullMemberSocialIdArr[$socialId] = array(
                'name' => $socialSite, 
                'memberSocialId' => $memberSocialId,
                'disabled' => $disabled,
                'avatar' => $avatar,
                'primaryAvatar' => $primaryAvatar
            );
        }

        return $fullMemberSocialIdArr;
        
    }
    
    public  function saveMemberSocialIds($siteArr, $useAvatarFromSocialSite, $memberId)
    {

        $socialIdSiteArr = SocialMedia::getSocialSiteIdArr();

        DB::beginTransaction();
        
        // delete existing relationships in table
        DB::table('member_social_ids')->where('member_id', $memberId)->delete(); 

        // save new 
    	if (count($siteArr) > 0) {
            $valuesArr = array();

            foreach($siteArr as $site => $arr) {

                $site = trim($site);
                $siteId = trim($arr['id']);
                if ($siteId == '' || $site == '' || !isset($socialIdSiteArr[$site])) {
                    continue;
                }
           
                $avatarSrc = isset($arr['avatar_src']) ? $arr['avatar_src'] : '';
                    $primaryAvatar = 0;                
                if ($useAvatarFromSocialSite == $site) {
                    $primaryAvatar = 1;
                }

    	        $valuesArr[] = array(
                    'member_id' => $memberId, 
                    'social_site' => $site, 
                    'member_social_id' => $siteId,
                    'disabled' => $arr['disabled'],
                    'primary_avatar' => $primaryAvatar,
                    'avatar' => $avatarSrc
                    
                   
                );
    	   }
    	   if (count($valuesArr) > 0) {
    	       DB::table('member_social_ids')->insert($valuesArr);
    	   }
           
           DB::commit();
          
    	}
        
    }
    
}