<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Member;

class SocialMedia extends Model {
    
    protected $guarded = [];
    
    public $socialMediaIds = array();
    
    public static function getCategory($slug)
    {

        // get category members
        $r = Member::getCategoryMembers($slug);
        printR($r);exit;
        // get member social media
        
    }
    

}