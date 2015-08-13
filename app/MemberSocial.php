<?php
namespace App;

use Twitter;
use DB;

class MemberSocial extends ModelNA
{

    protected $table = 'member_social_ids';

    public function getSocialMediaWithMemberIds(array $memberArr, $socialMediaId = false, $offset = 0, $limit = 6)
    {
        
        if (count($memberArr) ==0 ) {
            return array();
        }

        $contentArr = array();
        foreach($memberArr as $obj) {
            
            $q = "SELECT * FROM (";
            $q.= "SELECT social_media.id, social_media.member_id, written_at, social_media.member_social_id, ";
            $q.= "social_id, text, media_url, media_height ";
            $q.= "media_width, link, source ";
            $q.= "FROM social_media ";
            $q.= "INNER JOIN member_social_ids ON ";
            $q.= "(member_social_ids.member_id = social_media.member_id "; 
            $q.= "AND "; 
            $q.= "member_social_ids.social_site = social_media.source) ";
            $q.= "WHERE "; 
            $q.= "social_media.member_id = '" . (int)$obj->id . "' "; 
            $q.= "AND "; 
            $q.= "social_media.unpublish = '0' "; 
            $q.= "AND "; 
            $q.= "member_social_ids.disabled = 0 "; 
            if ($socialMediaId) {
                $q.= "AND "; 
                $q.= "social_media.id < $socialMediaId ";
            }
            $q.= "ORDER BY social_media.id DESC "; 
            $q.= ") AS tmp_table ";
            $q.= "GROUP BY tmp_table.id ";
            $q.= "ORDER BY tmp_table.id DESC ";
            $q.= "LIMIT 6 "; 
            $q.= "OFFSET 0";
            //echo $q."<br>";
            $r = DB::select($q);

            /*
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
                ->take($limit);
            //$r->get();

            echo $this->getQuery($r);exit();
             * 
             */
            
            // initialize array for ordering members by social media date
            $mostRecentMediaDateArr[$obj->id] = 0;

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
                
                // TODO move to social media entity?
                $text = Twitter::setSource($rowObj->source)->linkify($text);
                $text = html_entity_decode($inReplyToText) . " " . $text;
                $r[$i]->text = $text;
                
                // set age of post
                $age = $this->getAge($rowObj->written_at);// . "| " . $rowObj->written_at . " | ";
                $r[$i]->age = $age;

                // members with most recent social media are to appear first
                // set most recent social_media unixtime for member
                $ut = strtotime($rowObj->written_at);
                if ($mostRecentMediaDateArr[$rowObj->member_id] < $ut) {
                    $mostRecentMediaDateArr[$rowObj->member_id] = $ut;
                }
                
            }

            // TODO use SocialMediaEntity
            $contentArr[$obj->id] = $r;
 
        }

        // sort members based on most recent social media
        arsort($mostRecentMediaDateArr);
        $newMemberArr = [];
        foreach($mostRecentMediaDateArr as $memberId => $ut) {
            foreach($memberArr as $obj) {
                if ($obj->id == $memberId) {
                    $newMemberArr[] = $obj;
                }
            }
        }

        return array($newMemberArr, $contentArr);
        
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
