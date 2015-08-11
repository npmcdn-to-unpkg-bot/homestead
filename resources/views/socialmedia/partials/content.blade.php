<?php

$numCols = 2;

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
    echo $name;
    echo "</span>";
echo "</div>\n"; // close headerTextLeft

$displayArr = array(1 => 'firstDisplay', 2 => 'secondDisplay', 3 => 'thirdDisplay');
for($i=1; $i<=$numCols; $i++) {

    echo "<div class='contentAndFooterCont " . $displayArr[$i] . "'>";
    echo "<div class='contentCont ";
    if ($i == 1) {
        echo "firstContentCont";
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
    
    echo "<div class='footerCont";
    if ($i == 1) {
        echo " firstFooterCont";
    }

    echo "' id = 'footerCont_" . $memberId . "_" . $i . "'>";
    echo " &nbsp; ";
    echo "</div>";

    echo "</div>";
}



echo "</div>\n";//close rowCont

// feed nav
echo "<div class='contentNav' id='contentNav_" . $memberId . "'>\n";

    echo "<a class='navRight' href='javascript:void(0);' id='right_" . $memberId . "'>";
    echo "<div class='navButton'>&raquo;</div>";
    echo "</a>";

    echo "<a class='navLeft' href='javascript:void(0);' id='left_" . $memberId . "'>";
    echo "<div class='navButton'>&laquo;</div>";
    echo "</a>";

    echo "<a class='navReload' href='javascript:void(0);' id='reload_" . $memberId . "'>";        
    echo "<div class='navButton'>&#8634;</div>";
    echo "</a>\n";

echo "</div>\n";