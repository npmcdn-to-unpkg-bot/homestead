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
    
    public function getChildren($parentId)
    {
 
        $arr = $this->select()->join('category_parent_and_children', 'categories.id', '=', 'child_id')
                ->where('parent_id', '=', $parentId)
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
        
        $count = 0;
        $arr[] = $r[0];
        do {
            
            $r = DB::table('categories') 
                ->join('category_parent_and_children', 'child_id', '=', 'categories.id')
                ->where('categories.id', '=', $r[0]->parent_id)
                ->get();
            $arr[] = $r[0];
            if ($count++ >3 ) {
                echo $r->toSql();
                exit;
                break;
            }
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
    
    public function getCategoryNameWithId($id)
    {
        if (is_null($this->categoriesArr)) {
            $this->categoriesArr = $this->getCategoriesArr();
        }
        
        if (!isset($this->categoriesArr[$id]['display_name'])) {
            return false;
        }
        
        return $this->categoriesArr[$id]['display_name'];
        
    }
    
    public function getCategoryIdWithName($name)
    {
        if (is_null($this->categoriesArr)) {
            $this->categoriesArr = $this->getCategoriesArr();
        }
                
        foreach($this->categoriesArr as $id => $arr) {
            if (strtolower($arr['display_name']) == strtolower($name)) {
                return $id;
            }
        }
        
        return false;
        
    }

}
