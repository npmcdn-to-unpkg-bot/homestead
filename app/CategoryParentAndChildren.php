<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;
use DB;

class CategoryParentAndChildren extends Model {

    protected $table = 'category_parent_and_children';
    
    public function saveParentChild($parentIdArr, $childId, $deleteParentIdArr)
    {

        // TODO: transaction
        // delete existing parent-child relationships in table
        DB::table('category_parent_and_children')->where('child_id', $childId)->delete(); 

        // save new parent-child relationships
    	if (count($parentIdArr) > 0) {
    	   $valuesArr = array();
    	   $parentIdArr = array_unique($parentIdArr);
    	   foreach($parentIdArr as $parentId) {
    	       if (in_array($parentId, $deleteParentIdArr)) {
    	           continue;
    	       }
    	       $valuesArr[] = array('child_id' => $childId, 'parent_id' => $parentId);
    	   }
    	   if (count($valuesArr) > 0) {
    	       DB::table('category_parent_and_children')->insert($valuesArr);
    	   }
    	}
     
        
    }

    /*
     * Get parent ids associated with child_id
     */
    public function getSelectedParentIdNameArr($child_id)
    {
        return $this->select(array('categories.display_name as display_name', 'categories.id as parent_id'))
                    ->join('categories', 'categories.id', '=', 'category_parent_and_children.parent_id')
                    ->where('category_parent_and_children.child_id', '=', $child_id)
                    ->lists('display_name', 'parent_id');
    }

    /*
     * don't allow selecting the current id as a parent id by removing it from
     * list of parentIds
     */
    /* not used - done in makeDDArr as it is only specific to that
    public static function removeIds($parentIdArr, $currentId)
    {

        foreach($parentIdArr as $key => $val) {
            if ($key != 0 && $key == $currentId) {
                unset($parentIdArr[$key]);
            }
        }
        
        return ($parentIdArr);
        
    }
    */


    /**
     * Make array of id=>name for select drop down menu for associating a category with a parent
     * Don't allow the current category id be selectable as a parent
     * Don't allow already selected parent id selectable in dropdowns except for the drop down it is set as selected in
     */
    public function makeDDArr($parentIdNameArr, $selectedParentIdNameArr, $currentId)
    {

        $ddArr = array(0 => ' - none -');
        foreach($parentIdNameArr as $id => $name) {
            if ($currentId != $id && !isset($selectedParentIdNameArr[$id])) {
                $ddArr[$id] = $name;
            }
        }

        return $ddArr; 
        
    }
    
    private function buildTree(array $elements, $parentId = 0) 
    {

        $branch = array();
    
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['child_id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
    
        return $branch;        
        
    }

    /*
     * Retrieves hierarchy of parent-child category relationships 
     * 
     * Returns an array in format:
     * Array(
            [0] => Array
                (
                    [parent_id] => 0
                    [child_id] => 18
                    [children] => Array
                        (
                            [0] => Array
                                (
                                    [parent_id] => 18
                                    [child_id] => 49
    */
    public function getHierarchy()
    {
        
        $parentChildArr = array();
        $categoryModel = new Category();
        $parentArr = $categoryModel->getParents();
        foreach($parentArr as $id => $name) {
            $parentChildArr[] = array('parent_id' => 0, 'child_id' => $id);
        }

        $categoriesParentAndChildrenArr = $this->all();
        foreach($categoriesParentAndChildrenArr as $key => $obj) {
            $tmp = $obj->getAttributes();
            $parentChildArr[] = array('parent_id' => $tmp['parent_id'], 'child_id' => $tmp['child_id']);
        }

        $tree = $this->buildTree($parentChildArr);

        return $tree;
        
    }

}