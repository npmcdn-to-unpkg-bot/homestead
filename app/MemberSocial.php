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
    
    protected $table = 'member_social_ids';
    
    /*
     * Get members within a category and order members from most recent social media post to oldest
     */
    public function getMembersWithinSingleCategory($catId)
    {
        $q = "SELECT tmp_table.id, tmp_table.name, tmp_table.avatar, ";
        $q.= "DATE_FORMAT(tmp_table.written_at, '%b %d, %Y %h:%i %p') as written_at ";
        //$q.= ", tmp_table.text, tmp_table.social_id ";
        $q.= "FROM ";
        $q.="(";
        $q.= "SELECT members.id, members.name, members.avatar, social_media.written_at ";
        //$q.= ", social_media.text, social_media.social_id ";
        $q.= "FROM members ";
        $q.= "INNER JOIN member_categories ON members.id = member_categories.member_id ";
        $q.= "AND category_id = " . $catId . " ";
        $q.= "LEFT JOIN social_media ON members.id = social_media.member_id AND social_media.unpublish = 0 ";
        $q.= "ORDER BY social_media.written_at DESC ";      
        $q.= ") ";
        $q.= "AS tmp_table GROUP BY tmp_table.id  ORDER BY written_at DESC";

        $r = DB::select($q);   
        return $r;
        
        printR($r);exit;
        
        $q = "SELECT * FROM ";
        $q.="(";
        $q.= "SELECT * FROM social_media ";
        $q.= "";
        $q.= "WHERE member_id IN ('" . implode("',' ", $memberIdArr) . "') ";
        $q.= "AND unpublish = 0 ";
        $q.= "ORDER BY written_at DESC";
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
            ->orderBy('social_media.written_at', 'DESC')
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
    
    public function getSocialMediaWithMemberIds(array $memberArr, $socialMediaId = false, $offset = 0, $limit = 6)
    {
        if (count($memberArr) ==0 ) {
            return array();
        }

        $contentArr = array();
        foreach($memberArr as $obj) {

            $r = DB::table('social_media')
                ->select('social_media.*')
                ->join('member_social_ids', function($join)
                {
                    $join->on('member_social_ids.member_id', '=', 'social_media.member_id')
                         ->on('social_media.source','=', 'member_social_ids.social_site');
                })         
                ->where('social_media.member_id', '=', $obj->id)
                ->where('social_media.unpublish', '=', '0')
                ->where('member_social_ids.disabled', '=', '0');  
                
            if ($socialMediaId) {
                // get 'older' aka smaller id's
                $r = $r->where('social_media.id', '<', $socialMediaId);
            }

            $r = $r->orderBy('social_media.id', 'DESC')
                ->skip($offset)
                ->take($limit)
                ->get();

            //echo $this->getQuery($r);

            foreach($r as $i => $rowObj) {
                
                // format links
                $inReplyToText = '';
                $text = is_object($rowObj) ? $rowObj->text : '';
                // TODO check save setting in laravel for not decoding html
                preg_match("~&lt;reply&gt;&lt;(.*?)&lt;/reply&gt;~is", $text, $arr);
                if (isset($arr[0])) {
                    $inReplyToText = $arr[0];
                    $text = str_replace($inReplyToText, "", $text);
                }
                $text = Twitter::linkify($text);
                $text = html_entity_decode($inReplyToText) . " " . $text;
                $r[$i]->text = $text;
                
                // set age of post
                $age = $this->getAge($rowObj->written_at);
                $r[$i]->age = $age;
            }

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
    
    /*
     * In order to link up the same person on different social sites, try and match the social member id
     * without _ or numbers
     * eg. twitter screenname is wanda_june123 and instagrame username is wandajune. Since both were selected 
     * to be followed on both social sites, it is safe to say they're the same person
     * Be sure they aren't already linked before calling this method
     */
    public function getMemberIdWithSimilarSocialId($memberSocialId, $socialSite)
    {
        
        $memberSocialIdNoNumbers = preg_replace("~[0-9]~", "", $memberSocialId);
        $memberSocialIdNoUnderscore = str_replace("_", "", $memberSocialId);
        $memberSocialIdLettersOnly = preg_replace("~[0-9_]~", "", $memberSocialId);
        $memberSocialIdShort = substr($memberSocialIdNoUnderscore, 2, 12);
        
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
        
        if (count($r) >0 ) {
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
            return $r[0]->member_id;
        }
        
        $q = "SELECT id as member_id ";
        $q.= "FROM members ";
        $q.= "WHERE REPLACE(name, ' ', '') LIKE '" . $memberSocialIdShort . "%' ";
        $q.= "GROUP BY member_id";
        $r = DB::select($q);
        
        if (count($r) >0 ) {
            return $r[0]->member_id;
        }
        
        return false;

    }
    
}
