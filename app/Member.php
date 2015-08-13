<?php namespace App;

use App\ModelNA;
//use Illuminate\Database\Eloquent\Model;
use DB;

use App\CategoryParentAndChildren;
use App\Category;
use App\SocialMedia;

class Member extends ModelNA {
    
    //protected $guarded = [];
        
    protected $fillable = array('name', 'slug', 'avatar');
    
        
    /*
     * Get members within a category and order members from most recent social media post to oldest
     */
    public function getMembersWithinSingleCategory($catId)
    {
//        $q = "SELECT tmp_table.id, tmp_table.name, tmp_table.avatar, tmp_table.written_at ";
//        //$q.= ", DATE_FORMAT(tmp_table.written_at, '%b %d, %Y %h:%i %p') as written_at ";
//        $q.= "FROM ";
//        $q.="(";
//        $q.= "SELECT members.id, members.name, sm.written_at, member_social_ids.avatar ";
//        $q.= "FROM members ";
//        $q.= "INNER JOIN member_categories ON members.id = member_categories.member_id ";
//        $q.= "AND category_id = " . $catId . " ";
//        $q.= "LEFT JOIN social_media AS sm ON members.id = sm.member_id AND sm.unpublish = 0 ";
//        $q.= "INNER JOIN member_social_ids ON sm.member_id = member_social_ids.member_id ";
//        $q.= "AND primary_avatar = 1 AND disabled = 0 ";
//        $q.= "ORDER BY sm.written_at DESC ";
//        $q.= ") ";
//        $q.= "AS tmp_table ";
//        $q.= "GROUP BY tmp_table.id ";
//        $q.= "ORDER BY written_at DESC";
        
        $q = "SELECT members.id, members.name, avatar ";
        $q.= "FROM members ";
        $q.= "INNER JOIN member_categories ON members.id = member_categories.member_id ";
        $q.= "AND category_id = " . $catId . " ";
        $q.= "LEFT JOIN member_social_ids AS msi ON msi.member_id = members.id ";
        $q.= "AND msi.primary_avatar = 1 AND msi.disabled = 0 ";
        $q.= "GROUP BY members.id";
        $r = DB::select($q); 
        return $r;
        
        $memberIdArr = array_map(function ($o) {return $o->id;}, $r);
        
        $q = "SELECT sm.written_at, msi.avatar  FROM social_media AS sm ";
        $q.= "INNER JOIN member_social_ids AS msi ON (msi.member_id = sm.member_id ";
        $q.= "AND sm.member_id IN (" . implode(",", $memberIdArr) . ")) ";
        $q.= "WHERE msi.disabled = 0 ";
        $q.= "AND sm.unpublish = 0 ";
        $r = DB::select($q); 
        printR($r);exit;
        return $r;
        
    }

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
        
        // TODO: transaction
        // delete existing categories in table
        DB::table('member_categories')->where('member_id', $memberId)->delete(); 
        
        $valuesArr = array();
        foreach($categoryIdArr as $categoryId) {
    	   $valuesArr[] = array('member_id' => $memberId, 'category_id' => $categoryId );
    	}
    	
    	if (count($valuesArr) > 0) {
    	   DB::table('member_categories')->insert($valuesArr);
    	}
        
    }
    
    /*
     * Get members that have a parent, but no child
     */
    public function getNoChild($next = 0, $limit = 15)
    {
        
//        $q = 'SELECT *, count(*) as num '; 
//        $q.= 'FROM members ';
//        $q.= 'JOIN member_categories as mc ON members.id = mc.member_id ';
//        $q.= 'GROUP BY mc.member_id ';
//        $q.= 'HAVING num < 2 ';
//        $q.= "LIMIT $next, $limit";
        
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
//printR($siteArr);printR($avatarArr);exit;
        $socialIdSiteArr = SocialMedia::getSocialSiteIdArr();

        // delete existing relationships in table
        DB::table('member_social_ids')->where('member_id', $memberId)->delete(); 

        // save new 
    	if (count($siteArr) > 0) {
            $valuesArr = array();
            //$siteArr = array_unique($siteArr);
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
          
    	}
        
    }
    
}