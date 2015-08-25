<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Links extends Model
{
    
    protected $fillable = array('name', 'link');
    
    /**
     * Update the rank order of the links 
     * 
     * @param array $linkArr
     */
    public static function updateLinkRank(array $linkArr) 
    {
        
        foreach ($linkArr as $key => $id) {
            self::where('id', $id)->update(['rank' => $key]);
        }
        
    }
    
}
