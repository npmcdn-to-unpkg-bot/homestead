<?php
namespace App;

//use App\ModelNA;

class InstagramAdapter extends ModelNA implements SocialFeedInterface
{
    protected $friendsArr = array();
    
    public function __construct($screenName, $accessToken, $socialMediaObj)
    {

        $this->socialMediaObj = $socialMediaObj;
        $this->screenName = $screenName;
        $this->accessToken = $accessToken;
        
    }
    
    public function getFeed()
    {

        $maxId = \DB::table('social_media')
            ->where('source', '=', 'instagram')
            ->orderBy('social_id', 'DESC')
            ->take(1)
            ->pluck('social_id');
        $url = 'https://api.instagram.com/v1/users/self/feed?access_token=' . $this->accessToken;
        if (!empty($maxId)) {
            //$url.="&max_id=" . $maxId;
        }
        echo "<p>$url</p>";
        $r = json_decode($this->callCurl($url));

        if (!isset($r->data)) {
            return false;
        }
                
        $socialMediaArr = $this->parseFeed($r);

        return $socialMediaArr;
        
    }
        
    public function parseFeed($r)
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

    public function getFriends()
    {

        $url = 'https://api.instagram.com/v1/users/self/follows?count=500&access_token=' . $this->accessToken;
        
        $instagramUsersArr = array();
        do {

            $r = $this->callCurl($url);
            $r = json_decode($r);

            if (isset($r->error_type)) {
                return array('error' => $r->error_type);
            }

            $instagramUsersArr = array_merge($instagramUsersArr, $r->data);

        } while (isset($r->pagination->next_url) && !empty($url = $r->pagination->next_url));
        
        $this->parseFriends($instagramUsersArr);
        
        return array();
        
    }
    
    public function parseFriends(array $instagramUsersArr) 
    {
        /*
         * Array
                (
                    [0] => stdClass Object
                        (
                            [username] => matthewkenneycuisine
                            [profile_picture] => https://igcdn...jpg
                            [id] => 211313983
                            [full_name] => Matthew Kenney
                        )
         */
        $this->friendsArr = [];
        foreach($instagramUsersArr as $key => $obj) {

            $this->friendsArr[] = [
                'name' => $obj->full_name,
                'member_social_id' => $obj->username,
                'source' => 'instagram',
                'avatar' => $obj->profile_picture
            ];
            
        }
        
    }
    
    public function getFriendsArr()
    {
        return $this->friendsArr;
    }

     
}