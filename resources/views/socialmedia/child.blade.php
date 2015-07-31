@extends('app')

@section('content')

<?php

renderCategoryPath($catPathArr);

echo "<br>";

foreach($memberArr as $obj) {
    
    $memberId = $obj->id;
    $name = $obj->name;
    $avatar = $obj->avatar;
    
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
    
    for($i=1; $i<4; $i++) {
        
        echo "<div class='contentCont";
        if ($i == 1) {
            echo " firstContentCont";
        }
        echo "'>"; 
        
        echo "<div  id='thumbCont_" . $memberId . "_" . $i . "' class='thumbCont' style='display:none;'>";
        echo "<a target='_blank' href=''>";
        echo "<img class='thumb' ";
        echo "src='' ";
        //echo "width='" . $obj->media_width . "' ";
        //echo "height='" . $obj->media_height . "'";
        echo "width='100' ";
        //echo "height='100'";
        echo ">";
        echo "</a>";
        echo "</div>";
        
        echo "<div id='textCont_" . $memberId . "_" . $i . "' class='textCont'></div>";
        
        echo "</div>";// close contentCont
                
    }

    echo "<div style='clear:both;'></div>";
    echo "</div>";//close rowContentContainer
    
    echo "<div class='contentNav' id='contentNav_" . $memberId . "'>";
    echo "<div class='contentNavInner'>";
        echo "<a class='navRight' href='javascript:void(0);' id='right_" . $memberId . "'>";
        echo "<span class='navButton'>&raquo;</span>";
        echo "</a>";
        echo "<br>";
        echo "<a class='navLeft' href='javascript:void(0);' id='left_" . $memberId . "'>";
        echo "<span class='navButton'>&laquo;</span>";
        echo "</a>";
        echo "<Br>";
        echo "<a class='navReload' href='javascript:void(0);' id='reload_" . $memberId . "'>";        
        echo "<span class='navButton'>&#8634;</span>";
        echo "</a>";
    echo "</div>";
    echo "</div>";
    
    for($i=1; $i<4; $i++) {

        echo "<div class='footerCont";
        if ($i == 1) {
            echo " firstFooterCont";
        }
        
        echo "' id = 'footerCont_" . $memberId . "_" . $i . "'>";
        echo " &nbsp; ";
        echo "</div>";

    }
        
    echo "</div>";//close rowCont
    
    echo "<hr>";
   
}

//printR($catPathArr);
printR($memberArr);
printR($contentArr);



?>

<script>

<?php echo 'contentArr=' . json_encode($contentArr); ?>
    
$(document).ready(function() {
    
    for(var memberId in contentArr) {
        console.log('memberId: ' + memberId);

        for(var j in contentArr[memberId]) {
            
            var idStr = memberId + "_" + (parseInt(j) + 1);
            console.log('content key: ' + j);
            console.log('idStr: ' + idStr);
            var obj = contentArr[memberId][j];
            text = obj['text'];
            link = obj['link'];
            $("#textCont_" + idStr).html(text);
            if (obj['media_url'] != '') {
                $("#thumbCont_" + idStr).show();
                media_url = obj['media_url'];
                if (obj['source'] == 'twitter') {
                    media_url = media_url + ":thumb";
                }
                $("#thumbCont_" + idStr + " > a").attr("href", link);
                $("#thumbCont_" + idStr + " > a >.thumb").attr("src", media_url);

            }
            
            onLink = "<a target='_blank' href='" + link + "'>on " + obj['source'] + "&raquo;</a>";
            footerContent = obj['formatted_created_at'] + " " + onLink;
            $("#footerCont_" + idStr).html(footerContent);
            console.log(footerContent);
            
        }
    }

    $(".navRight").click(function() {

       console.log($(this).attr('id'));



    });



});

</script>


@endsection