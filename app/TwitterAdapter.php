<?php
namespace App;

//use Twitter;

class TwitterAdapter
{
    protected $memberArr = array();
    
    public function __construct($screenName)
    {

        $this->screenName = $screenName;
        
    }

    public function parseMembers($nextCursor = -1)
    {

        // get members being followed on twitter
        $paramArr = [
            'screen_name' => $this->screenName, 
            'skip_status' => true, 
            'include_user_entities' => false, 
            'cursor' => $nextCursor
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
            $r->users[0] = $mem; 
            
            $r->next_cursor_str = -1;
        }
        
        if (!isset($r->users)) {
            exit ('users is not a property of twitter result $r');
        }
        
        $this->setMemberArr($r);
               
        return $r->next_cursor_str;
        
    }
    
    protected function setMemberArr($r)
    {
        
        foreach($r->users as $obj) {

            //TODO make into an entity instead of an stdClass
            $mem = new \stdClass();
            // $text = iconv("UTF-8", "UTF-8//IGNORE", $text);
            // http://stackoverflow.com/questions/1401317/remove-non-utf8-characters-from-string
            $mem->name = mb_convert_encoding(trim($obj->name), 'UTF-8', 'UTF-8');
            $mem->memberSocialId = $obj->screen_name;
            $mem->avatar = $obj->profile_image_url;
            $mem->description = $obj->description;
            $mem->childId = 0;
            $mem->parentId = 0;
            $this->memberArr[strtolower($obj->screen_name)] = $mem;
            
        }
        
    }
    
    public function getMemberArr()
    {
        return $this->memberArr;
    }

    private function getFriendsIds($count = 2, $format = 'json')
    {

        $paramArr = ['screen_name' => $this->screenName, 'count' => $count, 'format' => $format, 'stringify_ids' => 1];
        $r = Twitter::getFriendsIds($paramArr);
        $obj = json_decode($r);
        return $obj->ids;
        
    }
     
}