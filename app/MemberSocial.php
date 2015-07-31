<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Twitter;
use DB;
use App\Scraper;
use App\Category;
use App\SocialMediaEntity;
//use CategoriesParentAndChildren;

class MemberSocial extends ModelNA
{
    
    public function getMembersWithinSingleCategory(Category $catObj)
    {
        // Get members within category         
        $r = DB::table('members')->select('name', 'avatar', 'members.id', 'member_categories.category_id')
            ->join('member_categories', function($join) use ($catObj)
            {
                $join->on('member_categories.member_id', '=', 'members.id')
                        ->where('member_categories.category_id', '=', $catObj->id);
            })->get();
        
        if (count($r) ==0 ) {
            return array();
        }
        
        // set member_id to be index value in array
        foreach($r as $obj) {
            $indexedMemberArr[$obj->id] = $obj;
        }

        return $indexedMemberArr;
        
    }
    
    public function getSocialMediaWithMemberIds(array $memberArr, $offset = 0, $limit = 3)
    {
        if (count($memberArr) ==0 ) {
            return array();
        }
        
        // TODO skip 'disabled' social_ids per member
        $contentArr = array();
        $memberIdcreatedAtArr = array();
        foreach($memberArr as $obj) {

            $r = DB::table('social_media')->select('social_media.*')
                ->join('member_social_ids', 'member_social_ids.member_id', '=', 'social_media.member_id')
                ->where('social_media.member_id', '=', $obj->id)
                ->where('disabled', '=', '0')
                ->orderBy('social_media.created_at', 'DESC')
                ->skip($offset)
                ->take($limit)
                ->get();
            //printR($r);exit;
            $contentArr[$obj->id][] = $r;
            
            // create array of times of content created so as to sort members by most recent content created
            foreach($r as $memberDBObj) {
                  if (!isset($memberIdcreatedAtArr[$obj->id])) {
                    $memberIdcreatedAtArr[$obj->id] = strtotime($memberDBObj->created_at);
                } else if ($memberIdcreatedAtArr[$obj->id] < strtotime($memberDBObj->created_at)) {
                    $memberIdcreatedAtArr[$obj->id] = strtotime($memberDBObj->created_at);
                }
            }
            if (!isset($memberIdcreatedAtArr[$obj->id])) {
                $memberIdcreatedAtArr[$obj->id] = 0;
            }
            arsort($memberIdcreatedAtArr, SORT_NUMERIC);
            
            $sortedContentArr = array();
            foreach($memberIdcreatedAtArr as $memberId => $ut) {
                $sortedContentArr[$memberId] = $contentArr[$memberId][0];
            }

        }
        
        return $sortedContentArr;
        
        $memberIdArr = array_map(function($o) { return $o->id; }, $memberArr);

        $q = "SELECT * FROM ";
        $q.="(";
        $q.= "SELECT * FROM social_media ";
        $q.= "WHERE member_id IN ('" . implode("',' ", $memberIdArr) . "') ";
        $q.= "AND unpublish = 0 ";
        $q.= "ORDER BY created_at DESC";
        $q.= ") ";
        $q.= "AS tmp_table GROUP BY member_id";
        $r = DB::select($q);

        return $r;
        
    }
    
               
    public function getMemberSocialIds($socialSite)
    {
        
        $r = DB::table('member_social_ids');
        $r->where('social_site', '=', $socialSite);
        $r->lists('member_social_id', 'member_id');
        return $r;
        
    }
    
    public function getMemberIdsWithMemberSocialIds(array $memberSocialIdArr, $socialSite)
    {
        $arr = DB::table('member_social_ids')
            ->where('social_site', '=', $socialSite)
            ->whereIn('member_social_id', $memberSocialIdArr)
            ->lists('member_id', 'member_social_id');
    
        return array_change_key_case($arr);
        
    }
    
}
