@extends('app')

@section('content')

   <h2 class='site_subject'>NBA Social Media</h2>
        <?php 
        renderTree($parentChildArr, $categoriesArr);
       ?> 
       
       
@endsection