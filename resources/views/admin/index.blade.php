<?php
use \App\Site;
?>
@extends('app')

@section('content')

<p><b>Admin</b></p>

<ul>
    
<?php

$subdomainArr = Site::getInstance()->getSubdomainData();
foreach($subdomainArr as $key => $arr) {
    if ($key == '' || $key == 'www') {
        continue;
    }

    echo "<li>";
    echo "<span class='mainPageCategoryName'>";
    echo $arr['nameShort'];
    echo "</span><br>";
    echo "<a href='" . $arr['baseUrl'] . "/members'>Members</a> ";
    echo " &nbsp; &#183; &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/members/create'>Members Create</a> ";
    echo " &nbsp; &#183; &nbsp; "; 
    echo "<a href='" . $arr['baseUrl'] . "/categories'>Categories</a> ";
    echo " &nbsp; &#183; &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/categories/create'>Categories Create</a> ";
    echo " &nbsp; &#183; &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/twitter/addstatus'>Add Twitter Status</a> ";
    echo " &nbsp; &#183; &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/twitter/addfriends'>Add Twitter Friends</a> "; 
    echo "</li>";

}

?>

</ul>
        
       
       
@endsection