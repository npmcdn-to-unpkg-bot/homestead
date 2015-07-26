<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\CategoryParentAndChildren;
use App\Category;

class Member extends Model {
    
    protected $guarded = [];
        
    protected $fillable = array('first_name', 'last_name', 'slug', 'avatar');

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
    
    public function getSocialIdSiteArr()
    {
        
        return array(
            'twitter' => 'Twitter',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'yelp' => 'Yelp',
            'youtube' => 'Youtube',	
            'pinterest' => 'Pinterest',
            'foursquare' => 'Foursquare',
        );
        
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
     * Get members that belong to a category
     */
    public  function getCategoryMembers($slug)
    {
        
        // get members with slug in category 
        $r = $this->select() 
                ->join('member_categories', 'members.id', '=', 'member_categories.member_id')
                ->join('categories', 'categories.id', '=', 'member_categories.category_id')
                ->where('categories.slug', '=', $slug)
                //->toSql();
                ->get();
        return $r;
        
    }

    /*
     * Create an array of social sites and the ids a member has for each one
     * 
     * returns array in format:
     * ['twitter'] = array('name' => 'Twitter', 'memberSocialId' => '3r9230rj23rj', 'disabled' => 1),
     * ['instagram'] = etc
     */
    public function getMemberSocialIdArr($memberId)
    {
        
        // get member's social ids (eg. twitter screen_name 'nba_playa')
        $memberSocialIdArr = DB::table('member_social_ids')
                ->where('member_id', '=', $memberId)
                ->get();

        // get array of social sites and set member's social ids for each site
        $socialIdSiteArr = $this->getSocialIdSiteArr();
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
        
        $socialIdSiteArr = $this->getSocialIdSiteArr();

        // TODO: transaction
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