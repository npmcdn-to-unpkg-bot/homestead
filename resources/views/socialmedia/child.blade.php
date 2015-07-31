@extends('app')

@section('content')

<?php

renderCategoryPath($catPathArr);

echo "<br>";

foreach($contentArr as $memberId => $arr) {
    
    $name = $memberArr[$memberId]->name;
    $avatar = $memberArr[$memberId]->avatar;
    
    echo "<div id = 'rowCont_" . $memberId . "' class='rowCont'>";
    
    echo "<div class='header'>";

    echo "<div class='avatarCont'>";
    echo "<img id='avatar_" . $memberId . "' src='" . $avatar . "' class='avatar'>";
    echo "</div>";

    echo "<div class='headerTextLeft'>";
    echo "<span class='memberName'>";
    echo $name . " | " . $memberId;
    echo "</span>";
    echo "</div>";

    echo "<div style='clear:both;'></div>";

    echo "</div>"; // close header

    echo "<div style='clear:both;'></div>";
    
    echo "<div class='rowContentCont'>";// maintains correct width even if member doesn't have content
    
    unset($key);
    $footer = '';
    // use firstId and lastId to retrieve content via the navigation
    $firstId = 0;
    $lastId = 0;
    foreach($arr as $key => $obj) {
        
        if ($key == 0) {
            $firstId = $obj->id;
        }
        
        $idStr = $obj->id . "_" . $obj->social_id;
        
        echo "<div class='contentCont";
        if ($key ==0 ) {
            echo " firstContentCont";
        }
        echo "' id='contentCont_" . $idStr . "'>";
        
        if ($obj->media_url !='' ) {
            echo "<div class='thumbCont'>";
            echo "<a target='_blank' href='" . $obj->link . "'>";
            echo "<img class='thumb' ";
            echo "src='" . $obj->media_url . ":thumb' ";
            //echo "width='" . $obj->media_width . "' ";
            //echo "height='" . $obj->media_height . "'";
            echo "width='100' ";
            //echo "height='100'";
            echo ">";
            echo "</a>";
            echo "</div>";
        }
        
        $lastId = $obj->id;

        //echo "<div class='textCont'>";
        echo Twitter::linkify($obj->text);
        //echo "</div>";
        
        echo "</div>";// close contentCont
        
        $footer.= "<div class='contentFooter";
        if ($key ==0 ) {
            $footer.= " firstContentFooter";
        }
        
        $footer.= "' id='footer_" . $idStr . "'>";
        $footer.= "<a target='_blank' href='" . $obj->link . "'>on " . $obj->source . "&raquo;</a>";
        $footer.= "</div>";
                
    }
    
    echo "<div style='clear:both;'></div>";
    echo "</div>";//close rowContentContainer
    
    echo "<div class='contentNav' id='contentNav_" . $idStr . "'>";
    echo "<div class='contentNavInner'>";
        echo "<a class='navRight' href='javascript:void(0);' id='right_" . $memberId . "_" . $lastId . "'>";
        echo "<span class='navButton'>&raquo;</span>";
        echo "</a>";
        echo "<br>";
        echo "<a class='navLeft' href='javascript:void(0);' id='left_" . $memberId . "_" . $firstId . "'>";
        echo "<span class='navButton'>&laquo;</span>";
        echo "</a>";
        echo "<Br>";
        echo "<a class='navReload' href='javascript:void(0);' id='reload_" . $memberId . "'>";        
        echo "<span class='navButton'>&#8634;</span>";
        echo "</a>";
    echo "</div>";
    echo "</div>";

    echo $footer;
    if (!isset($key)) {
        echo "<div class='contentFooter firstContentFooter'> &nbsp; </div>";
        echo "<div class='contentFooter'> &nbsp; </div>";
        echo "<div class='contentFooter'> &nbsp; </div>";
    } elseif ($key === 1) {
        echo "<div class='contentFooter'> &nbsp; </div>";
    } else if ($key === 0) {
        echo "<div class='contentFooter'> &nbsp; </div>";
        echo "<div class='contentFooter'> &nbsp; </div>";
    }
        
    echo "</div>";//close rowCont
    
    echo "<Hr>";
   
}

printR($catPathArr);
printR($memberArr);
printR($contentArr);

?>

<script>
$(document).ready(function() {

$(".navRight").click(function() {

   console.log($(this).attr('id'));
   
});



});

</script>


@endsection