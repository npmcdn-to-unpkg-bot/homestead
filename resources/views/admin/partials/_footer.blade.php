<div style='clear:both;'></div>
<hr>

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
    echo "<a href='" . $arr['baseUrl'] . "/members'>Members:</a> ";
    echo " &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/members/create'> Create</a> ";
    echo " &nbsp; &#183; &nbsp; "; 
    echo "<a href='" . $arr['baseUrl'] . "/categories'>Categories:</a> ";
    echo " &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/categories/create'>Create</a> ";
    echo " &nbsp; ";
    echo "<a href='" . $arr['baseUrl'] . "/categories/show'>Sort</a> ";
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
<div id="dialog" title="Confirmation Required">  Are you sure about this?</div>​
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src='/js/admin.js'></script>