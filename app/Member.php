<?php namespace App;

use App\ModelNA;
//use Illuminate\Database\Eloquent\Model;
use DB;

use App\CategoryParentAndChildren;
use App\Category;
use App\SocialMedia;

class Member extends ModelNA {
    
    protected $guarded = [];
        
    protected $fillable = array('name', 'slug', 'avatar');
    


    
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
        
        $q = 'SELECT *, count(*) as num '; 
        $q.= 'FROM members ';
        $q.= 'JOIN member_categories as mc ON members.id = mc.member_id ';
        $q.= 'GROUP BY mc.member_id ';
        $q.= 'HAVING num < 2 ';
        $q.= "LIMIT $next, $limit";
        
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
//            ->toSql();
//exit($r);
        return $r;
        
        $r = $this->select('members.*', DB::raw('count(*) as num'))
                ->join('member_categories as mc', 'mc.member_id', '=', 'members.id')
                ->groupBy('mc.member_id')
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
        
        $r = $this->select('members.*')
                ->leftJoin('member_categories as mc', 'mc.member_id', '=', 'members.id')
                ->whereNull('mc.category_id')
                ->skip($next)->take($limit)
                ->get();
        
        return $r;               
        
    }
    
    /*
     * Get members and their child category using slug to query with
     */
    public function getMembersAndChildCategoryWithSlug($slug, $next = 0, $limit = 15)
    {
        
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
       /*
        $r = $this->select() 
                ->join('member_categories', 'members.id', '=', 'member_categories.member_id')
                ->join('categories', 'categories.id', '=', 'member_categories.category_id')
                ->where('categories.slug', '=', $slug)
                ->skip($next)->take($limit);
        */
        /*$r = DB::table('categories') 
                ->join('member_categories', 'member_categories.category_id', '=', 'categories.id')
                ->join('members', 'members.id', '=', 'member_categories.member_id')
                ->where('categories.slug', '=', $slug)
                ->skip($next)->take($limit);
         * 
         */
        if (0) {
            echo $this->getQuery($r);
        } else {
            return $r->get();
        }
        
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
            foreach($memberSocialIdArr as $key => $obj) {
                if ($obj->social_site == $socialId) {
                    $memberSocialId = $obj->member_social_id;
                    $disabled = $obj->disabled;
                    break;
                }
            }
            $fullMemberSocialIdArr[$socialId] = array(
                'name' => $socialSite, 
                'memberSocialId' => $memberSocialId,
                'disabled' => $disabled
            );
        }

        return $fullMemberSocialIdArr;
        
    }
    
    public  function saveMemberSocialIds($siteArr, $memberId)
    {
        
        $socialIdSiteArr = SocialMedia::getSocialSiteIdArr();

        // TODO: transaction
        // 
        // delete existing parent-child relationships in table
        DB::table('member_social_ids')->where('member_id', $memberId)->delete(); 

        // save new parent-child relationships
    	if (count($siteArr) > 0) {
            $valuesArr = array();
            //$siteArr = array_unique($siteArr);
            foreach($siteArr as $site => $arr) {

                $site = trim($site);
                $siteId = trim($arr['id']);
                if ($siteId == '' || $site == '' || !isset($socialIdSiteArr[$site])) {
                    continue;
                }
    	        $valuesArr[] = array(
                   'member_id' => $memberId, 
                   'social_site' => $site, 
                   'member_social_id' => $siteId,
                   'disabled' => $arr['disabled'] 
                   
                );
    	   }
    	   if (count($valuesArr) > 0) {
    	       DB::table('member_social_ids')->insert($valuesArr);
    	   }
    	}
        
    }
    
}