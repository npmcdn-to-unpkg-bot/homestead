@extends('app')

@section('content')

   <h2 class='site_subject'>NBA Social Media</h2>
        <?php 
        foreach($parentChildArr as $itemArr) {
            $route = 'socialmedia/' . $categoriesArr[$itemArr['child_id']]['slug'];
            echo renderItem($itemArr, $categoriesArr, $route);
       }
       ?> 
       
       
@endsection