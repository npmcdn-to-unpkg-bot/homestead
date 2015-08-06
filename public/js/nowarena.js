$(document).ready(function() {
    
    width = $(window).width();

    if (width >= 1900) {
        var numMediaDisplayed = 3;
    } else if (width >= 1000) {
        var numMediaDisplayed = 2;
        $(".thirdContentCont").hide();
        $(".thirdFooterCont").hide();
    } else {
        var numMediaDisplayed = 1;
        $(".secondContentCont").hide();
        $(".secondFooterCont").hide();        
        $(".thirdContentCont").hide();
        $(".thirdFooterCont").hide();
    }
    
    var memberIdSocialIdArr = [];
    
    displayMedia(contentArr);
    
    function displayMedia(contentArr) {
        
        for(var memberId in contentArr) {

            memberIdSocialIdArr[memberId] = {};
            memberIdSocialIdArr[memberId].lastSocialMediaId = 0;
            memberIdSocialIdArr[memberId].firstSocialMediaId = 0;
            $("#contentCount_" + memberId).text(contentArr[memberId].length);

            if (contentArr[memberId].length == 0) {
                displayEndMsg(memberId, 1);
                continue;
            }

            for(var j in contentArr[memberId]) {
                
                j = parseInt(j);

                // firstSocialMediaId
                if (j === 0) {
                    memberIdSocialIdArr[memberId].firstSocialMediaId = contentArr[memberId][j]['id'];
                    //console.log(j + '.) displayMedia() firstSocialMediaId:'+memberIdSocialIdArr[memberId].firstSocialMediaId);
                }
                
                var obj = contentArr[memberId][j];
        
                displayMediaBlock(obj, memberId, j);

                if (j !==0 && (j % (numMediaDisplayed - 1)  === 0) || (j == 0 && numMediaDisplayed ==1)) {
                    // eg. j is position '2' and there are 5 social media content for memberId and numMediaDisplayed is 3
                    memberIdSocialIdArr[memberId].lastSocialMediaId = obj['id'];

                    break;
                } else if (j === contentArr[memberId].length - 1 ) {// && contentArr[memberId].length < numMediaDisplayed) {
                    // eg. j is position '0' and there is 1 social media content for memberId 
                    //console.log('j:'+j);
                    memberIdSocialIdArr[memberId].lastSocialMediaId = obj['id'];
                    displayEndMsg(memberId, j+2);
                }

            }
            
        }
    
    }
    
    function displayEndMsg(memberId, index) {
        
        $("#textCont_" + memberId + "_" + index).html('End of feed reached');
        $("#thumbCont_" + memberId + "_" + index).hide();
        $("#footerCont_" + memberId + "_" + index).html(" &nbsp; ");
        if (index == 1) {
            displayBlank(memberId, 2);
            displayBlank(memberId, 3);
        } else if (index == 2) {
            displayBlank(memberId, 3);
        }
        
    }
    
    function displayBlank(memberId, index) {
        if ( $("#textCont_" + memberId + "_" + index).length >0 ) {
            $("#textCont_" + memberId + "_" + index).html(' &nbsp; '); 
            $("#thumbCont_" + memberId + "_" + index).hide();
            $("#footerCont_" + memberId + "_" + index).html(' &nbsp;');
        }
    }
    
    function displayMediaBlock(obj, memberId, j) {
        
        var idStr = memberId + "_" + (j + 1);
                       
        //text = 'social_media.id: ' + obj['id'] + ' | social_id: ' + obj['social_id'] +" | " + obj['text'];
        text = obj['text'];
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
        footerContent = obj['age'] + " " + onLink;
        $("#footerCont_" + idStr).html(footerContent);
                
    }
    
    /*
     * Set array of member's social media for display
     */
    function displaySubContentArr(subMemberArr, memberId) {
        
        subContentArr = [];
        subContentArr[memberId] = subMemberArr;

        if (subContentArr[memberId].length >0 ) {
            displayMedia(subContentArr);
        } else {
            displayEndMsg(memberId,1);
        }       
        
    }
    
    function getJson(memberId, lastSocialMediaId) {
        
        q_str = '?member_id=' + memberId + '&social_media_id=' + lastSocialMediaId;
        $.getJSON( "/socialmedia/getmembersocialmedia" + q_str, function( data ) {
            // set where we left off in the member's social_media content arr
            startIndex = contentArr[memberId].length;
            // add the retrieved social media content to member's content array
            $.each(data['memberContentArr'][memberId], function(key, val) {
                contentArr[memberId].push(val);
            });
            // extract the retrieved social media starting from where we left off
            subMemberArr = contentArr[memberId].slice(startIndex, startIndex + numMediaDisplayed);

            displaySubContentArr(subMemberArr, memberId);
    
        });

    }

    $(".navRight").click(function() {

        var hasMediaNotDisplayed = false;
        
        memberId = $(this).attr('id').substring(6);
        // see if there is any more social media already loaded but not displayed
        if (contentArr[memberId].length > numMediaDisplayed) {
            subMemberArr = [];
            lastSocialMediaId = memberIdSocialIdArr[memberId].lastSocialMediaId;
            for(j in contentArr[memberId]) {
                obj = contentArr[memberId][j];
                j = parseInt(j);
                //console.log(contentArr[memberId].length);
                if (obj['id'] == lastSocialMediaId && contentArr[memberId].length > j + 1) {
                    hasMediaNotDisplayed = true;
                    subMemberArr = contentArr[memberId].slice(j + 1, j + numMediaDisplayed + 1);
                    break;
                }
            }

        }
        
        if (hasMediaNotDisplayed) {
            displaySubContentArr(subMemberArr, memberId);
        } else {
            getJson(memberId, memberIdSocialIdArr[memberId].lastSocialMediaId);
        }

    });

    $(".navLeft").click(function() {

        memberId = $(this).attr('id').substring(5);
        //console.log('member_id:'+memberId);
        //console.log('first:'+memberIdSocialIdArr[memberId].firstSocialMediaId);
        if (memberIdSocialIdArr[memberId].firstSocialMediaId > 0) {
            for(var j in contentArr[memberId]) {

                if (contentArr[memberId][j]['id'] == memberIdSocialIdArr[memberId].firstSocialMediaId) {
                //console.log('nav left:'+ j +'|id:'+contentArr[memberId][j]['id']);
                //console.log('nav left first social media id:'+memberIdSocialIdArr[memberId].firstSocialMediaId);       
                    j = parseInt(j);
                    //console.log($("#textCont_" + memberId + "_1").text());
                    if ($("#textCont_" + memberId + "_1").text() == 'End of feed reached') {
                        //console.log('length:'+contentArr[memberId].length);
                        // TODO redo this without relying on reading text
                        startPos=j;
                        endPos = j + numMediaDisplayed;
                    }else{
                        startPos = j - numMediaDisplayed;
                        endPos = j;                        
                        if (startPos <=0 ) {
                            startPos = 0;
                            endPos = startPos + numMediaDisplayed;    
                        }     
                    }
                    //console.log('startPos:'+startPos);
                    //console.log('endPos:'+endPos);
                    
                    subMemberArr = contentArr[memberId].slice(startPos, endPos);
                    //console.log('typeof content:' + typeof contentArr[memberId]);
                    //console.log('typeof:'+typeof subMemberArr[memberId]);
                    displaySubContentArr(subMemberArr, memberId);
                }
            }
        }


    });
    
    
    $(".navReload").click(function() {
        memberId = $(this).attr('id').substring(7);
        //reset contentArr
        contentArr[memberId] = [];
        getJson(memberId, 0);

    });
    
    //
    // Prev and Next navigation in parent layout
    //
       
    $(".childNext, .childPrev").click(function() {
        
        id = $(this).attr('id');
        childId = id.substr(6);
        clickAction = $(this).attr('class');

        // find the visible member
        for(var i in displayArr[childId]) {
            
            i = parseInt(i);

            if (displayArr[childId][i] == 'block') {
                
                // hide current visible member
                memberId = memberIdArr[childId][i];
                $("#stack_" + memberId).hide();
                displayArr[childId][i] = 'none';
                
                if (clickAction == 'childNext') {
                    // set childNext values
                    if (typeof displayArr[childId][(i + 1)] != 'undefined') {
                        // next member to be made visible
                        newIndex = i + 1;
                        // next member name in 'next' link to be made visible
                        if (typeof displayArr[childId][(newIndex + 1)] != 'undefined') {
                            nextLinkIndex = newIndex + 1;
                        } else {
                            nextLinkIndex = 0;
                        }
                    } else {
                        newIndex = 0;
                        nextLinkIndex = 1;
                    }
                } else {
                    // set childPrev values
                    if (typeof displayArr[childId][(i - 1)] != 'undefined') {
                        // prev member to be made visible
                        newIndex = i - 1;
                        // prev member name in 'next' link to be made visible
                        // NOTE 'prev' link is clicked, but there is no previous member name in previous link
                        // only member name in 'next' link gets updated
                        nextLinkIndex = i;
                    } else {
                        newIndex = displayArr[childId].length - 1;
                        nextLinkIndex = 0;
                    } 
                }
                newMemberId = memberIdArr[childId][newIndex];
                $("#stack_" + newMemberId).show();
                displayArr[childId][newIndex] = 'block';

                memberName = memberNameArr[childId][nextLinkIndex];
                $('.next_member_' + childId).text(memberName);
                
                break;
            }
        }

    });
    
    for(var childId in memberNameArr) {
        if (memberNameArr[childId].length == 1) {
            continue;
        }
        $(".next_member_" + childId).html(memberNameArr[childId][1]);

    }   
    

});