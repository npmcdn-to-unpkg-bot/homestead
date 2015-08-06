<?php

$numCols = 3;

$memberId = $obj->id;
$name = $obj->name;
$avatar = $obj->avatar;

echo "\n\n<div id = 'rowCont_" . $memberId . "' class='rowCont'>\n";

// avatar
echo "<div class='leftBar'>";
    echo "<div class='avatarHolder'>";
    echo "<img id='avatar_" . $memberId . "' src='" . $avatar . "' class='avatar'>";
echo "</div>\n";

echo "</div>";

// member name
echo "<div class='headerTextLeft'>";
echo "<span class='memberName'>";
echo $name;// . " | " . $memberId;
echo "</span>";
echo " <span style='display:none;' id='contentCount_" . $memberId . "'></span>";

echo "</div>\n";

echo "<div class='rowContentCont'>";// maintains correct width even if member doesn't have content

for($i=1; $i<=$numCols; $i++) {

    echo "<div class='contentCont";
    if ($i == 1) {
        echo " firstContentCont";
    } else if ($i == 2) {
        echo " secondContentCont";
    } else {
        echo " thirdContentCont";
    }
    echo "'>\n"; 

    echo "<div  id='thumbCont_" . $memberId . "_" . $i . "' class='thumbCont'>";
    echo "<a target='_blank' href=''>";
    echo "<img class='thumb' ";
    echo "src='' ";
    echo ">";
    echo "</a>";
    echo "</div>\n";

    echo "<div id='textCont_" . $memberId . "_" . $i . "' class='textCont'></div>\n";

    echo "</div>\n";// close contentCont

}

//echo "<div style='clear:both;'></div>";

for($i=1; $i<=$numCols; $i++) {

    echo "<div class='footerCont";
    if ($i == 1) {
        echo " firstFooterCont";
    } else if ($i == 2) {
        echo " secondFooterCont";
    } else {
        echo " thirdFooterCont";
    }

    echo "' id = 'footerCont_" . $memberId . "_" . $i . "'>";
    echo " &nbsp; ";
    echo "</div>";

}

echo "</div>\n";//close rowContentContainer


// feed nav
echo "<div class='contentNav' id='contentNav_" . $memberId . "'>\n";

    echo "<div>";
    echo "<a class='navRight' href='javascript:void(0);' id='right_" . $memberId . "'>";
    echo "<span class='navButton'>&raquo;</span>";
    echo "</a>";
    echo "<br>";
    echo "</div>";
    
    echo "<div style='margin-top:-10px;'>";
    echo "<a class='navLeft' href='javascript:void(0);' id='left_" . $memberId . "'>";
    echo "<span class='navButton'>&laquo;</span>";
    echo "</a>";
    echo "</div>";
    
    echo "<div>";
    echo "<a class='navReload' href='javascript:void(0);' id='reload_" . $memberId . "'>";        
    echo "<span class='navButton' style='font-size:34px;'>&#8634;</span>";
    echo "</a>\n";
    echo "</div>";
    
echo "</div>\n";

echo "</div>\n";//close rowCont





