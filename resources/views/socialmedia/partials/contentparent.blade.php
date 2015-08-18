<?php

$numCols = 2;

$memberId = $obj->id;
$name = $obj->name;
$avatar = $obj->avatar;

echo "\n\n<div id = 'rowCont_" . $childId . "' class='rowCont'>\n";
?> 

<div class='leftBar'>
    <div class='avatarHolder'>
        <img src='' class='avatar'>
    </div>
</div>

<div class='headerTextLeft'>
    <span class='memberName'> &nbsp; </span>
    
    <div class='circleGCont' id="circleG_<?php echo $childId;?>">
        <div id="circleG_1" class="circleG"></div>
        <div id="circleG_2" class="circleG"></div>
        <div id="circleG_3" class="circleG"></div>
    </div>
    
</div>

<?php
$displayArr = array(1 => 'firstDisplay', 2 => 'secondDisplay', 3 => 'thirdDisplay');
for($i=1; $i<=$numCols; $i++) {

    echo "<div class='contentAndFooterCont " . $displayArr[$i] . "'>";
    echo "<div class='contentCont ";
    if ($i == 1) {
        echo "firstContentCont";
    }
    echo "'>\n"; 

    echo "<div  id='thumbCont_" . $childId . "_" . $i . "' class='thumbCont'>";
    echo "<a target='_blank' href=''>";
    echo "<img class='thumb' ";
    echo "src='' ";
    echo ">";
    echo "</a>";
    echo "</div>\n";

    echo "<div id='textCont_" . $childId . "_" . $i . "' class='textCont'></div>\n";

    echo "</div>\n";// close contentCont
    
    echo "<div class='footerCont";
    if ($i == 1) {
        echo " firstFooterCont";
    }

    echo "' id = 'footerCont_" . $childId . "_" . $i . "'>";
    echo "<span class='ageLink'><span class='ageText'></span><span class='onLink'></span></span>";    
    echo "<a data-childid='" . $childId . "' ";
    echo "data-boxnum='" . $i . "' ";
    echo "class='expandLink' id='expandLink_" . $childId . "_" . $i . "' href='javascript:void(0);'>expand &darr;</a> ";
    echo "<a data-childid='" . $childId . "' ";
    echo "data-boxnum='" . $i . "' ";
    echo "class='shrinkLink' id='shrinkLink_" . $childId . "_" . $i . "' href='javascript:void(0);'>shrink &uarr;</a> ";
    
    echo "</div>";

    echo "</div>";
}



echo "</div>\n";//close rowCont

// feed nav
echo "<div class='contentNav'>\n";

    echo "<a class='navRight' data-childid='" . $childId . "' href='javascript:void(0);'>";
    echo "<div class='navButton'>&raquo;</div>";
    echo "</a>";

    echo "<a class='navLeft' data-childid='" . $childId . "' href='javascript:void(0);'>";
    echo "<div class='navButton'>&laquo;</div>";
    echo "</a>";

    echo "<a data-childid='" . $childId . "' class='navReload' href='javascript:void(0);'>";        
    echo "<div class='navButton'>&#8634;</div>";
    echo "</a>\n";

echo "</div>\n";