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
    /*
     * Get members within a category and order members from most recent social media post to oldest
     */
    public function getMembersWithinSingleCategory(Category $catObj)
    {
        $q = "SELECT tmp_table.id, tmp_table.name, tmp_table.avatar, ";
        $q.= "DATE_FORMAT(tmp_table.created_at, '%b %d, %Y %h:%i %p') as created_at ";
        //$q.= ", tmp_table.text, tmp_table.social_id ";
        $q.= "FROM ";
        $q.="(";
        $q.= "SELECT members.id, members.name, members.avatar, social_media.created_at ";
        //$q.= ", social_media.text, social_media.social_id ";
        $q.= "FROM members ";
        $q.= "INNER JOIN member_categories ON members.id = member_categories.member_id ";
        $q.= "AND category_id = " . $catObj->id . " ";
        $q.= "LEFT JOIN social_media ON members.id = social_media.member_id AND social_media.unpublish = 0 ";
        $q.= "ORDER BY social_media.created_at DESC ";      
        $q.= ") ";
        $q.= "AS tmp_table GROUP BY tmp_table.id  ORDER BY created_at DESC";

        $r = DB::select($q);   
        return $r;
        
        printR($r);exit;
        
        $q = "SELECT * FROM ";
        $q.="(";
        $q.= "SELECT * FROM social_media ";
        $q.= "";
        $q.= "WHERE member_id IN ('" . implode("',' ", $memberIdArr) . "') ";
        $q.= "AND unpublish = 0 ";
        $q.= "ORDER BY created_at DESC";
        $q.= ") ";
        $q.= "AS tmp_table GROUP BY member_id";
        $r = DB::select($q);
        
             
        $r = DB::table('members')->select('name', 'avatar', 'members.id', 'member_categories.category_id')
            ->join('member_categories', function($join) use ($catObj)
            {
                $join->on('member_categories.member_id', '=', 'members.id')
                        ->where('member_categories.category_id', '=', $catObj->id);
            })
            ->leftJoin('social_media', 'social_media.member_id' ,'=', 'members.id')
            ->orderBy('social_media.created_at', 'DESC')
            ->get();
        
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

            $r = DB::table('social_media')
                ->select('social_media.*', DB::raw("DATE_FORMAT(social_media.created_at, '%b %d, %Y %h:%i %p') as formatted_created_at"))
                ->join('member_social_ids', 'member_social_ids.member_id', '=', 'social_media.member_id')
                ->where('social_media.member_id', '=', $obj->id)
                ->where('disabled', '=', '0')
                ->orderBy('social_media.created_at', 'DESC')
                ->skip($offset)
                ->take($limit)
                ->get();
            //printR($r);exit;
            $contentArr[$obj->id] = $r;
            //printR($contentArr);exit;
            /*
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
            */
        }
        
        return $contentArr;
        
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
