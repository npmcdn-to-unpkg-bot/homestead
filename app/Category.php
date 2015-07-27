<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Category extends Model {

    protected $guarded = [];
    
    public function getParents()
    {
 
        $arr = $this->select()->join('category_parent_and_children', 'categories.id', '=', 'child_id')
                ->where('parent_id', '=', '0')
                ->lists('display_name', 'child_id');

        return $arr;
        
    }
        
    public function getCategoryPath($slug)
    {

        $r = DB::table('categories') 
            ->join('category_parent_and_children', 'child_id', '=', 'categories.id')
            ->where('categories.slug', '=', $slug)
            ->get();
        
        if (count($r) == 0) {
            return array();
        }

        // if it is a parent, that's it, done
        if (count($r) == 1 && $r[0]->parent_id == 0) {
            return $r;
        }
        
        $arr[] = $r[0];
        do {
            
            $r = DB::table('categories') 
                ->join('category_parent_and_children', 'child_id', '=', 'categories.id')
                ->where('categories.id', '=', $r[0]->parent_id)
                ->get();
            $arr[] = $r[0];

        } while($r[0]->parent_id >0 );

        // since we built the array from child up to parent, reverse it for top down display
        return array_reverse($arr);
        
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
