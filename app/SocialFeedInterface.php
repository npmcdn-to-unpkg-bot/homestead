<?php
namespace App;

/*
 * 
 */
interface SocialFeedInterface {

    public function getFriends();
    public function parseFriends(array $arr);
    public function getFeed();
    public function parseFeed(array $arr);
    public function getFriendsArr();
    
}
