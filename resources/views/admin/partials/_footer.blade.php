<div style='clear:both;'></div>
<hr>
<!--
<form action='/members/search' style='text-align:center;'>";        
<input type='text' name='search' value='" . htmlentities($search) . "' style='width:100px;font-size:10px;'>";
<input type='submit' value='Search'>
</form>
-->
<ul style="margin-left:-130px;margin-right:-130px;">
    
<?php

use \App\Site;

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
    echo "<a href='" . $arr['baseUrl'] . "/twitter/getfeed'>Add Twitter Feed</a> ";
    echo " &nbsp; &#183; &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/twitter/getfriends'>Add Twitter Friends</a> "; 
    echo " &nbsp; &#183; &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/instagram/getfriends'>Add Instagram Friends</a> "; 
    echo " &nbsp; &#183; &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/instagram/getfeed'>Add Instagram Feed</a> ";
    echo "</li>";

}

?>

</ul>