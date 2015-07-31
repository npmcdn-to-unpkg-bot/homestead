<?php

if (!function_exists('str_slug')) {
    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    function str_slug($title, $separator = '_')
    {
        return Str::slug($title, $separator);
    }
}

function renderCategoryPath($catPathArr, $route = '/socialmedia/')
{
    
    if (count($catPathArr) >0 ) {

        foreach($catPathArr as $key => $obj) {
            if ($key >0 ) {
                echo " &raquo; ";
            }
            echo "<a href='" . $route . $obj->slug . "'>" . $obj->display_name . "</a>";
        }
        echo "<br>";
    }
    
}

/**
 * View helper for formating category hierarchies with ul/li
 * 
 */
function renderItem($itemArr, $categoriesArr, $route, $slug = '') 
{

    $id = $itemArr['child_id'];
    $out = "<span class='category_name'><a href='";
    $out.= '/' . $route . '/' . $slug;
    $out.= "'>";
    $out.= $categoriesArr[$id]['display_name'];
    $out.= "</a></span>";
 
    if (isset($itemArr['children'])) {
        $out.= "<ul class='category_ul'>";
        foreach ($itemArr['children'] as $child) {
            $slug = $categoriesArr[$child['child_id']]['slug'];
            $out.= "<li>" . renderItem($child, $categoriesArr, $route, $slug) . "</li>";
        }
        $out.= "</ul>";
    }
    
    
    return $out;
    
}

function renderTree($parentChildArr, $categoriesArr, $route = 'socialmedia') 
{
    foreach($parentChildArr as $itemArr) {
        
        $id = $itemArr['child_id'];
        $slug = $categoriesArr[$id]['slug'];
        //printR($itemArr);       
        //printR($parentChildArr);        
        //printR($categoriesArr);
        echo renderItem($itemArr, $categoriesArr, $route, $slug);
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
        $out.= "<input type='checkbox' name='category_id[]' value='$id' $checked> ";

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