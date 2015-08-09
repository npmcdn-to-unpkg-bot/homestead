<?php
use \App\Site;
?>
@extends('app')

@section('content')

<p>
    Browse social media posted by leaders within the categories below. 
</p>
<ul>
    
<?php

$subdomainArr = Site::getInstance()->getSubdomainData();
foreach($subdomainArr as $key => $arr) {
    if ($key == '' || $key == 'www') {
        continue;
    }

    echo "<li><a href='" . $arr['baseUrl'] . "/socialmedia";
    if ($arr['categoryDepth'] < 3) {
        echo '/all';
    }
    echo "'>";
    echo "<span class='mainPageCategoryName'>";
    echo $arr['nameShort'];
    echo "</span>";
    echo "</a><br>";
    echo $arr['description'];
    echo "</li>";

}

?>

</ul>

       
       
@endsection