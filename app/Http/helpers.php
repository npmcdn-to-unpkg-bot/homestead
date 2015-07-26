<?php

/**
 * View helper for formating category hierarchies with ul/li
 * 
 */
function renderItem($itemArr, $categoriesArr, $route) 
{

    $id = $itemArr['child_id'];
    $out = "<span class='category_name'><a href='";
    //$out.= route($route);
    $out.= $route;
    $out.= "'>";
    $out.= $categoriesArr[$id]['display_name'];
    $out.= "</a></span>";
 
    if (isset($itemArr['children'])) {
        $out.= "<ul class='category_ul'>";
        foreach ($itemArr['children'] as $child) {
            $out.= "<li>" . renderItem($child, $categoriesArr, $route) . "</li>";
        }
        $out.= "</ul>";
    }
    
    
    return $out;
    
}

function renderTree($parentChildArr, $categoriesArr) 
{
    foreach($parentChildArr as $itemArr) {
        if (!isset($categoriesArr[$itemArr['child_id']])) {
            //echo 'asdf';
        }else{
            $route = '/socialmedia/' . $categoriesArr[$itemArr['child_id']]['slug']; 
            echo renderItem($itemArr, $categoriesArr, $route);
        }
    }
}


/**
 * View helper for formating category hierarchies as a form with checkboxes with ul/li
 * 
 */
function renderCheckboxItem($itemArr, $categoriesArr, $memberCategoryIdArr) 
{

    $out = '';
    $id = $itemArr['child_id'];
    
    if (isset($categoriesArr[$id])) {

        $out = "<span class='category_name'>";

        $checked = '';
        if (in_array($id, $memberCategoryIdArr)) {
            $checked = ' checked';
        }

        //$out.= {{ Form::checkbox('category_id[]', $id, $bool) }}
        $out.= "<input type='checkbox' name='category_id[]' value='$id' $checked>";

        $out.= $categoriesArr[$id]['display_name'];
        $out.= "</span>";

        if (isset($itemArr['children'])) {
            $out.= "<ul class='category_ul'>";
            foreach ($itemArr['children'] as $child) {
                $out.= "<li>" . renderCheckboxItem($child, $categoriesArr, $memberCategoryIdArr) . "</li>";
            }
            $out.= "</ul>";

        }
    
    }
    
    return $out;
    
}