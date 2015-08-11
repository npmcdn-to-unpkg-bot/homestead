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
            $q.= "(`member_social_ids`.`member_id` = `social_media`.`member_id` "; 
            $q.= "AND "; 
            $q.= "`social_media`.`source` = `member_social_ids`.`social_site`) ";
            $q.= "WHERE "; 
            $q.= "`social_media`.`member_id` = '" . (int)$obj->id . "' "; 
            $q.= "AND "; 
            $q.= "`social_media`.`unpublish` = '0' "; 
            $q.= "AND "; 
            $q.= "`member_social_ids`.`disabled` = 0 "; 
            $q.= "ORDER BY `social_media`.`id` DESC "; 
            $q.= ") AS tmp_table ";
            $q.= "GROUP BY tmp_table.id ";
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
                $text = Twitter::linkify($text);
                $text = html_entity_decode($inReplyToText) . " " . $text;
                $r[$i]->text = $text;
                
                // set age of post
                $age = $this->getAge($rowObj->written_at);
                $r[$i]->age = $age;
            }

            // TODO use SocialMediaEntity
            $contentArr[$obj->id] = $r;
 
        }
      
        return $contentArr;
        
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
