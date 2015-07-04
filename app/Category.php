<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    protected $guarded = [];
    
    public static function getParents()
    {
 
        $arr = self::where('is_a_parent', '=', 1)->lists('display_name', 'id');
        if (!is_array($arr)){ 
            $arr = array();
        }
        //$arr+=array(0 => ' - none - ');
        return $arr; 
        //return DB::table('categories')->where('is_a_parent', '>', 0)->orderBy('name', 'desc')->get();
        
    }

    /*
     * Make categories object into a lookup table
     */
    public static function getCategoriesArr($categoriesObj = '')
    {

        if ($categoriesObj == '') {
            $categoriesObj = self::all();
        }
        
    	$categoriesArr = array();
		foreach($categoriesObj as $key => $obj) {
		    $categoriesArr[$obj->id]['display_name'] = $obj->display_name;
		    $categoriesArr[$obj->id]['slug'] = $obj->slug;
		}
		
		return $categoriesArr;
        
    }

}
