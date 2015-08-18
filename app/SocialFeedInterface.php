<?php
namespace App;

/*
 * 
 */
interface SocialFeedInterface {

    public function getFriends();
    public function parseFriends(array $arr);
    public function getFeed();
    public function parseFeed($arr);
    public function getFriendsArr();
    
}
