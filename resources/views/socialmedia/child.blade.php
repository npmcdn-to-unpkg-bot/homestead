@extends('app')

@section('content')

<?php

renderCategoryPath($catPathArr);

echo "<br>";

foreach($memberArr as $i => $obj) {
    
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
    echo $name . " | " . $memberId . " | " . count($contentArr[$memberId]);
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
    
    var numMediaDisplayed = 3;
    var memberIdSocialIdArr = [];
    
    displayMedia(contentArr);
    
    function displayMedia(contentArr) {

        for(var memberId in contentArr) {

            //console.log('memberId: ' + memberId);
            memberIdSocialIdArr[memberId] = {lastSocialId: 0};
            memberIdSocialIdArr[memberId].firstSocialId = 0;

            if (contentArr[memberId].length == 0) {
                $("#textCont_" + memberId + "_1").html('End of feed reached');
                continue;
            }

            for(var j in contentArr[memberId]) {
                
                j = parseInt(j);

                // firstSocialId
                if (j === 0) {
                    memberIdSocialIdArr[memberId].firstSocialId = contentArr[memberId][j]['social_id'];
                }
                
                var obj = contentArr[memberId][j];
        
                displayMediaBlock(obj, memberId, j);

                //console.log(footerContent);

                // lastSocialId
                if (j !==0 && (j % (numMediaDisplayed - 1)  === 0)) {
                    // eg. j is position '2' and there are 5 social media content for memberId and numMediaDisplayed is 3
                    memberIdSocialIdArr[memberId].lastSocialId = obj['social_id'];
                    break;
                } else if (j === contentArr[memberId].length - 1 ) {// && contentArr[memberId].length < numMediaDisplayed) {
                    // eg. j is position '0' and there is 1 social media content for memberId 
                    memberIdSocialIdArr[memberId].lastSocialId = obj['social_id'];
                    $("#textCont_" + memberId + "_" + (j+2)).html('End of feed reached');
                }

            }
        }
    
    }
    
    function displayMediaBlock(obj, memberId, j) {
        
        var idStr = memberId + "_" + (j + 1);
                       
        text = obj['social_id'] +" : " + obj['text'];
        link = obj['link'];
        $("#textCont_" + idStr).html(text);
        if (obj['media_url'] !== '') {
            $("#thumbCont_" + idStr).show();
            media_url = obj['media_url'];
            if (obj['source'] == 'twitter') {
                media_url = media_url + ":thumb";
            }
            $("#thumbCont_" + idStr + " > a").attr("href", link);
            $("#thumbCont_" + idStr + " > a >.thumb").attr("src", media_url);
            $("#thumbCont_" + idStr).show(); 
        } else {
            $("#thumbCont_" + idStr).hide(); 
        }

        onLink = "<a target='_blank' href='" + link + "'>on " + obj['source'] + "&raquo;</a>";
        footerContent = obj['formatted_created_at'] + " " + onLink;
        $("#footerCont_" + idStr).html(footerContent);
        
        return obj;
                
    }
    
    /*
     * Set array of member's social media for display
     */
    function displaySubContentArr(subMemberArr, memberId) {
        
        subContentArr = [];
        subContentArr[memberId] = subMemberArr;

        if (subContentArr[memberId].length >0 ) {
            displayMedia(subContentArr);
        }       
        
    }

    $(".navRight").click(function() {

        var hasMediaNotDisplayed = false;
        
        memberId = $(this).attr('id').substring(6);
        // see if there is any more social media already loaded but not displayed
        if (contentArr[memberId].length > numMediaDisplayed) {
            subMemberArr = [];
            lastSocialId = memberIdSocialIdArr[memberId].lastSocialId;
            for(j in contentArr[memberId]) {
                obj = contentArr[memberId][j];
                if (obj['social_id'] == lastSocialId) {
                    hasMediaNotDisplayed = true;
                    subMemberArr = contentArr[memberId].slice(parseInt(j) + 1, j + numMediaDisplayed);
                    break;
                }
            }

        }
        

        if (hasMediaNotDisplayed) {
            displaySubContentArr(subMemberArr, memberId);
        } else {
            //get from server
        }


    });

    $(".navLeft").click(function() {

        memberId = $(this).attr('id').substring(5);
        console.log('member_id:'+memberId);
        // can we nav left
        canNavLeft = false;
        if (memberIdSocialIdArr[memberId].firstSocialId > 0) {
            for(var j in contentArr[memberId]) {
                if (contentArr[memberId][j]['social_id'] == memberIdSocialIdArr[memberId].firstSocialId) {
                    subMemberArr = contentArr[memberId].slice(parseInt(j) - numMediaDisplayed, numMediaDisplayed);
                    displaySubContentArr(subMemberArr, memberId);
                }
            }
        }


    });

});

</script>


@endsection