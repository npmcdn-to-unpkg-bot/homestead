<?php
namespace App;

use App\ModelNA;

class InstagramAdapter extends ModelNA
{
    protected $memberArr = array();
    
    public function __construct($screenName, $accessToken, $socialMediaObj)
    {

        $this->socialMediaObj = $socialMediaObj;
        $this->screenName = $screenName;
        $this->accessToken = $accessToken;
        
    }
    
    public function addStatus()
    {


        if (1) {
            
            $maxId = \DB::table('social_media')
                ->where('source', '=', 'instagram')
                ->orderBy('social_id', 'DESC')
                ->take(1)
                ->pluck('social_id');
            $url = 'https://api.instagram.com/v1/users/self/feed?access_token=' . $this->accessToken;
            if (!empty($maxId)) {
                $url.="&max_id=" . $maxId;
            }
            echo $url."<br><br>";
            $r = json_decode($this->callCurl($url));
            
        } else {
            $r[0] = new \stdClass();
            $r[0]->created_at = 'Wed Jul 29 17:01:55 +0000 2015';
            $r[0]->user = new \stdClass();
            $r[0]->user->screen_name = 'LAClippers';
            $r[0]->id_str = 626447502926024704;
            $r[0]->text = 'RT @mexime1t: The @LAClippers know how to treat their fans!Thank you for my #GearUpLA AUTOGRAPHED @paulpierce34 hat! #ClipperNation http://…';
            $r[0]->in_reply_to_status_id = '';
            $r[0]->in_reply_to_screen_name ='';
            $r[0]->retweeted_status = new \stdClass();
            $r[0]->retweeted_status->text = 'The @LAClippers know how to treat their fans!Thank you for my #GearUpLA AUTOGRAPHED @paulpierce34 hat! #ClipperNation http://t.co/rXq8sbyR7M';
            $r[0]->entities = new \stdClass();
            $r[0]->entities->urls = array();
            $r[0]->entities->media[0] = new \stdClass();
            $r[0]->entities->media[0]->media_url = 'http://pbs.twimg.com/media/CLGTDzZUYAAhTwK.jpg';
            $r[0]->entities->media[0]->sizes = new \stdClass();
            $r[0]->entities->media[0]->sizes->thumb = new \stdClass();
            $r[0]->entities->media[0]->sizes->thumb->w = 150;
            $r[0]->entities->media[0]->sizes->thumb->h = 150;
        }

        if (!isset($r->data)) {
            return false;
        }
                
        $socialMediaArr = $this->parseStatus($r);

        return $socialMediaArr;
        
    }
        
    public function parseStatus($r)
    {

        $socialMediaArr = [];
        foreach($r->data as $key => $obj) {

            $memberSocialId = strtolower($obj->user->username);
            $socialId = $obj->id;
            $link = $obj->link;
            $mediaUrl = $obj->images->thumbnail->url;
            $mediaHeight = $obj->images->thumbnail->height;
            $mediaWidth = $obj->images->thumbnail->width;
            $text = '';
            $written_at = date("Y-m-d H:m:i", $obj->created_time);
            if (!empty($obj->caption->text)) {
                $text = $obj->caption->text;
                $written_at = date("Y-m-d H:m:i", $obj->caption->created_time);                
            }

            $socialMediaArr[] = [
                'memberSocialId' => $memberSocialId,
                'memberId' => 0,
                'socialId' => $socialId,
                'text' => $text,
                'link' => $link,
                'mediaUrl' => $mediaUrl,
                'mediaHeight' => $mediaHeight,
                'mediaWidth' => $mediaWidth,
                'source' => 'instagram',
                'written_at' => $written_at
            ];
            
        }
        
        return $socialMediaArr;

    }

    public function parseMembers()
    {

        $url = 'https://api.instagram.com/v1/users/self/follows?access_token=' . $this->accessToken;
        
        do {

            $r = $this->callCurl($url);
            $r = json_decode($r);

            //if (++$count==5)break;
            if (isset($r->error_type)) {
                return array('error' => $r->error_type);
            }

            foreach($r->data as $key => $obj) {
                $membersArr[strtolower($obj->username)] = $obj->full_name;
            }

        } while (isset($r->pagination->next_url) && !empty($url = $r->pagination->next_url));
           
        // do a bulk remove from membersArr of users already in the database
        $membersArr = $this->socialMediaObj->getMemberSocialIdsNotInDB($membersArr);

        // see if they have the same username as a twitter account, if so, use the member id to
        // associate this instagram account with it
        // TODO Move this into MemberSocial and make less specific to instagram
        $memberSocialObj = new \App\MemberSocial();
        $notOnSiteArr = [];
        foreach($membersArr as $instagramName => $name) {
         
            $arr = $memberSocialObj->getMemberIdsWithMemberSocialIds(array($instagramName), 'twitter');
            if (count($arr) ==1 ) {
                $memberId = array_shift($arr);
                $valuesArr = array(
                    'social_site' => 'instagram', 
                    'member_id' => $memberId, 
                    'member_social_id' => $instagramName
                );
                $memberSocialObj->insert($valuesArr);
                continue;
            }
            
            if (($memberId = $memberSocialObj->getMemberIdWithSimilarSocialId($instagramName, 'instagram')) != false) { 
                $valuesArr = array(
                    'social_site' => 'instagram', 
                    'member_id' => $memberId, 
                    'member_social_id' => $instagramName
                );
                $memberSocialObj->insert($valuesArr);
                continue;
            }
        
            $notOnSiteArr[$instagramName] = $name;
            
        }
        //printR($notOnSiteArr);
        return $notOnSiteArr;
        
    }
    
    protected function setMemberArr($r)
    {
        
        foreach($r->users as $obj) {

            $mem = new \App\MemberEntity();
            $mem->setName($obj->name)
                ->setMemberSocialIdArr($obj->screen_name, 'twitter')
                ->setAvatar($obj->profile_image_url)
                ->setDescription($obj->description)
                ->setChildId(0)
                ->setParentId(0);
            $this->memberArr[strtolower($obj->screen_name)] = $mem;
            
        }
        
    }
    
    public function getMemberArr()
    {
        return $this->memberArr;
    }

     
}