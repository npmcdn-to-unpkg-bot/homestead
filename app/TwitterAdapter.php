<?php
namespace App;

//use Twitter;

class TwitterAdapter
{
    protected $friendsArr = array();
    
    public function __construct($screenName)
    {

        $this->screenName = $screenName;
        
    }
    
    public function getFeed()
    {
        
        if (1) {
            
            $since_id = \DB::table('social_media')
                ->where('source', '=', 'twitter')
                ->orderBy('social_id', 'DESC')
                ->take(1)
                ->pluck('social_id');

            $paramArr = [
                'count' => 200,
                'include_entities' => 1
            ];
            if ($since_id) {
                $paramArr['since_id'] = $since_id;
            }
            //printR($paramArr);
            $r = \Twitter::getHomeTimeline($paramArr);
            
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

        if (count($r) == 0) {
            return false;
        }
                
        $socialMediaArr = $this->parseFeed($r);

        return $socialMediaArr;
        
    }
        
    public function parseFeed($r)
    {
        
        $socialMediaArr = [];
        foreach($r as $key => $obj) {
            
            $memberSocialId = strtolower($obj->user->screen_name);
            $socialId = $obj->id_str;
            $link = 'https://twitter.com/' . $memberSocialId . '/status/' . $socialId;
            $mediaUrl = '';
            $mediaHeight = '';
            $mediaWidth = '';
            if (!empty($obj->entities->media)) {
                $media = $obj->entities->media;
                if (isset($media[0]->media_url)) {
                    $mediaUrl = $media[0]->media_url;
                    $mediaHeight = $media[0]->sizes->thumb->h;
                    $mediaWidth = $media[0]->sizes->thumb->w;
                }
            }

            // replace shortened urls with full urls
            $text = $obj->text;
            if (!empty($obj->entities->urls)) {
                foreach($obj->entities->urls as $key => $urlObj) {
                    $text = str_replace($urlObj->url, $urlObj->expanded_url, $text);
                }
            }
            
            // for retweets, set full retweeted text to $text 
            if (!empty($obj->retweeted_status)) {
                $retweetedText = $obj->retweeted_status->text;
                $text = preg_match("~RT @[^:]+: (.*?)~is", $text, $arr);
                $text = $arr[0] . $retweetedText;
            }
            
            // add 'in reply to' link to text
            if (!empty($obj->in_reply_to_status_id)) {
                $replyLink = "<reply>";
                $replyLink.= "<a target='_blank' ";
                $replyLink.= "href='https://twitter.com/" . $obj->in_reply_to_screen_name . "/status/";
                $replyLink.= $obj->in_reply_to_status_id . "'>(in reply to " . $obj->in_reply_to_screen_name . ")";
                $replyLink.= "</a>";
                $replyLink.= "</reply> ";
                $text = $replyLink . $text;
            }

            $written_at = date("Y-m-d H:m:i", strtotime($obj->created_at));

            $socialMediaArr[] = [
                'memberSocialId' => $memberSocialId,
                'memberId' => 0,
                'socialId' => $socialId,
                'text' => $text,
                'link' => $link,
                'mediaUrl' => $mediaUrl,
                'mediaHeight' => $mediaHeight,
                'mediaWidth' => $mediaWidth,
                'source' => 'twitter',
                'written_at' => $written_at
            ];
            
        }
        
        return $socialMediaArr;

    }

    public function getFriends($nextCursor = -1)
    {

        // get members being followed on twitter
        $paramArr = [
            'screen_name' => $this->screenName, 
            'skip_status' => true, 
            'include_user_entities' => false, 
            'cursor' => $nextCursor,
            'count' => 200
        ];
        
        if (1) {
            
             $r = \Twitter::getFriends($paramArr);

        } else {
            
            // test data
            $r = new \stdClass();
            
            $mem = new \stdClass();
            $mem->screen_name = 'Adrien_Payne';
            $mem->name = 'Adreian Payne';
            $mem->description = 'Blah blah northwest minnesota Timberwolves';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/3439961848/64f4a9275de184af5e381486492000b2_normal.jpeg';
            $r->users[2] = $mem; 
            
            $mem = new \stdClass();
            $mem->screen_name = 'Kobe_Bryant';
            $mem->name = 'Kobe Bryan';
            $mem->description = 'Blah blah LAKERS ASDF';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/3439961848/64f4a9275de184af5e381486492000b2_normal.jpeg';
            $r->users[1] = $mem; 
            
            $mem = new \stdClass();
            $mem->name = 'Jason Thompson';
            $mem->screen_name = 'jtthekid';
            $mem->description = 'PF/C for Philadelphia 76ers. iG: Jtthekid Manager: @dschwartze1 Mr. 519+';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/618625788460560384/iwS9dPYE_normal.jpg';
            $r->users[3] = $mem; 
            
            $r = new \stdClass();
            $mem = new \stdClass();
            $mem->name = 'DeAndre Jordan';
            $mem->screen_name = 'deandrejordan6';
            $mem->description = 'love comedy, netflix, and gluten cookies.';
            $mem->profile_image_url = 'http://pbs.twimg.com/profile_images/618625788460560384/iwS9dPYE_normal.jpg';
            $r->users[0] = $mem; 
            
            $r->next_cursor_str = -1;
        }
        
        if (!isset($r->users)) {
            exit ('users is not a property of twitter result $r');
        }
        
        $this->parseFriends($r);
               
        return $r->next_cursor_str;
        
    }
    
    protected function parseFriends($twitterFriendsObj)
    {
        $this->friendsArr = [];
        foreach($twitterFriendsObj->users as $obj) {

            $this->friendsArr[] = [
                'name' => $obj->name,
                'member_social_id' => $obj->screen_name,
                'source' => 'twitter',
                'avatar' => $obj->profile_image_url,
                'description' => $obj->description,
            ];
            
        }
        
    }

    
    public function getFriendsArr()
    {
        return $this->friendsArr;
    }
     
}