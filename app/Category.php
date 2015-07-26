<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Category extends Model {

    protected $guarded = [];
    
    public function getParents()
    {
 
        $arr = $this->select()->join('category_parent_and_children', 'categories.id', '=', 'child_id')
                ->where('parent_id', '=', '0')
                ->lists('display_name', 'id');

        return $arr;
        
    }

    /*
     * Make categories object into a lookup table
     */
    public function getCategoriesArr($categoriesObj = '')
    {

        if ($categoriesObj == '') {
            $categoriesObj = $this->all();
        }
        
    	$categoriesArr = array();
		foreach($categoriesObj as $key => $obj) {
		    $categoriesArr[$obj->id]['display_name'] = $obj->display_name;
		    $categoriesArr[$obj->id]['slug'] = $obj->slug;
		}
		
		return $categoriesArr;
        
    }

}
