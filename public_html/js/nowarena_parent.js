$(document).ready(function() {
    
    // contentArr is set in body of page
    
    // number of columns to display
    var numMediaDisplayed = 2;
    
    // array of properties with member id as the key and the first 
    // and last social id as properties
    var memberIdSocialIdArr = [];
    

    $( window ).resize(function() {
        mngResize();
    });
    
    // expand link and shrink link reference this
    var originalTextContParentHeight = ($(".textCont").parent().height());

    /*
     * 
     * Resize blocks that require it after window is resized
     * @returns {undefined}
     */
    function mngResize() {
        
        width = $(window).width();

        if (width >= 1000) {
            numMediaDisplayed = 2;
            //remove any inline style set when column was single            
            $(".contentAndFooterCont").css('style', '');           
            $(".secondDisplay").css('display', 'inline-block');
            $(".thirdDisplay").hide();
        } else {
            numMediaDisplayed = 1;
            $(".secondDisplay").hide();
            $(".thirdDisplay").hide();
            // if it's only one column being displayed, contentAndFooterCont is
            // about 100px less then the width
            if (width < 740 && width >= 270) {
                resizedWidth = width-100;
                $(".contentAndFooterCont").css('width', resizedWidth);
            }
        }
        
        mngChildNextLinks();

    }
    
    /*
     * Display 'end' of feed message and reformat blocks
     * @param int memberId
     * @param int index
     * @returns {undefined}
     */
    function displayEndMsg(childId, index) {

        //displayBlank(childId, index);
        $("#textCont_" + childId + "_" + index).html('End of feed reached');

        $("#thumbCont_" + childId + "_" + index).hide();
        $("#footerCont_" + childId + "_" + index + " > .ageLink > .ageText").html('&nbsp;');
        $("#footerCont_" + childId + "_" + index + " > .ageLink > .onLink").html('&nbsp;');
        $("#expandLink_" + childId + "_" + index).css('display','');

        if (index == 1) {
            displayBlank(childId, 2);
        }
        
    }
    
    function displayBlank(childId, index) {
        if ( $("#textCont_" + childId + "_" + index).length >0 ) {
            $("#textCont_" + childId + "_" + index).html(' &nbsp; '); 
            $("#thumbCont_" + childId + "_" + index).hide();
            $("#footerCont_" + childId + "_" + index + " > .ageLink > .ageText").html('&nbsp;');
            $("#footerCont_" + childId + "_" + index + " > .ageLink > .onLink").html('&nbsp;');        
        }
    }
    
    /*
     * If text is hidden in an overflow, an 'expand' link is shown. Clicking
     * the expand link reveals the hidden text
     */
    $(".expandLink").click(function() {
        
        childId = $(this).data('childid');
        boxNum = $(this).data('boxnum');

        var cssPathStr = "#rowCont_" + childId + " > .contentAndFooterCont > .contentCont";
        // set textContainer height to real height of text
        var realHeight = $("#textCont_" + childId + "_" + boxNum).height();

        var hiddenHeight = $(cssPathStr).height();
        $(cssPathStr).height(realHeight);
        // set leftBar to additional height
        var leftBarHeight = $("#rowCont_" + childId + " > .leftBar").height();
        var increaseBy = realHeight - hiddenHeight;

        $("#rowCont_" + childId + " > .leftBar").height(leftBarHeight + increaseBy);

        // if in the parent layout, expand parent holder as well - layers
        // were creating non clickable links below them even though layers
        // were not visible
        if ($(this).closest('.parentHolder').length ) {
            parentHolderHeight = $(this).closest('.parentHolder').height();
            $(this).closest('.parentHolder').height(parentHolderHeight + increaseBy);
        }

        mngExpandShrinkLinks(childId);      
    
    });
    
    /*
     * If an expand link has been clicked, a shrink link to re-hide the text
     * is shown
     */
    $(".shrinkLink").click(function() {
        childId = $(this).data('childid');
        boxNum = $(this).data('boxnum');
        var cssPathStr = "#rowCont_" + childId + " > .contentAndFooterCont > .contentCont";
        $(cssPathStr).css('height', '');
        $("#rowCont_" + childId + " > .leftBar").css('height', '');    
        if ($(this).closest('.parentHolder').length ) {
            $(this).closest('.parentHolder').css('height', '');
        }

        mngExpandShrinkLinks(childId);
    });
    
    /*
     * Enable aborting of json call by setting response globally
     * @type @exp;$@call;getJSON
     */
    var jsonGlobal;
    
    /*
     * Gets social media in json for member
     * @param int memId
     * @param int lastSocialMediaId
     * @param int childId
     * @returns {undefined}
     */
    function getJson(memId, lastSocialMediaId, childId, callDisplayChildHeader, nextLinkIndex) {

        $("#circleG_" + childId).show();
        q_str = '?member_id=' + memId + '&social_media_id=' + lastSocialMediaId;
        jsonGlobal = $.getJSON( "/socialmedia/getmembersocialmedia" + q_str, function( data ) {
            // set where we left off in the member's social_media content arr
            startIndex = contentArr[memId].length;
            // add the retrieved social media content to member's content array
            $.each(data['memberContentArr'][memId], function(key, val) {
                contentArr[memId].push(val);
            });
            // extract the retrieved social media starting from where we left off
            subMemberArr = [];
            subMemberArr[memId] = contentArr[memId].slice(startIndex, startIndex + numMediaDisplayed);
            displayChildRow(memId, childId, subMemberArr);
            
            if (callDisplayChildHeader) {
                // get index position of member in array and use that to extract
                // data from other arrays
                currentPosition = 0;
                for(var indexPosition in memIdArr[childId]) {
                    if (memIdArr[childId][indexPosition] == memId) {
                        currentPosition = parseInt(indexPosition);
                    }
                }
                nextLinkIndex = 0;
                if (typeof memIdArr[childId][parseInt(currentPosition + 1)] != 'undefined') {
                    nextLinkIndex = parseInt(currentPosition + 1);
                }

                // name, avatar
                displayChildHeader(childId, currentPosition);
                // set next member name in prev next nav
                newMemberName = getMemberName(childId, nextLinkIndex);
                $('.next_child_id_' + childId).text(newMemberName);
            }
            
            $("#circleG_" + childId).hide();
    
        });

    }
    
    /*
     * Cancel any previously existing json requests 
     * and hide any loader animation
     */
    $(".navRight, .navLeft, .navReload, .childNext, .childPrev").click(function(){
        childId = $(this).data('childid');    
        $("#circleG_" + childId).hide();
        // abort any previous calls
        if (typeof jsonGloba != 'undefined') {
            jsonGlobal.abort();
        }
    });
   
        
    /*
     * Navigate social media feed to the right.
     * Looks for already loaded data and if none found, call server
     */
    $(".navRight").click(function() {
        
        var hasMediaNotDisplayed = false;
        
        childId = $(this).data('childid');
        memId = childIdMemIdVisibleArr[childId];

        // if there is any more social media already loaded, 
        // see if it should be displayed
        if (contentArr[memId].length > numMediaDisplayed) {
 
            lastSocialMediaId = memberIdSocialIdArr[memId].lastSocialMediaId;
            for(j in contentArr[memId]) {

                j = parseInt(j);                
                obj = contentArr[memId][j];

                if (obj['id'] == lastSocialMediaId && contentArr[memId].length - 1 > j) {
                    // get the next social media
                    startPos = j + 1;
                    endPos = startPos + numMediaDisplayed;
                    hasMediaNotDisplayed = true;
                    subMemberArr = [];
                    subMemberArr[memId] = contentArr[memId].slice(startPos, endPos);
                    displayChildRow(memId, childId, subMemberArr);                    
                    break;
                }
            }

        }
        
        if (hasMediaNotDisplayed == false) {
            getJson(memId, memberIdSocialIdArr[memId].lastSocialMediaId, childId, false, false);
        }

    });

    /*
     * Navigate social media feed to the left
     */
    $(".navLeft").click(function() {

        childId = $(this).data('childid');
        memId = childIdMemIdVisibleArr[childId];
        
        if (memberIdSocialIdArr[memId].firstSocialMediaId > 0) {
            for(var j in contentArr[memId]) {
                if (contentArr[memId][j]['id'] == memberIdSocialIdArr[memId].firstSocialMediaId) {    
                    j = parseInt(j);
                    if ($("#textCont_" + childId + "_1").text() == 'End of feed reached') {
                        // TODO redo this without relying on reading text
                        startPos=j;
                        endPos = j + numMediaDisplayed;
                    }else{
                        startPos = j - numMediaDisplayed;
                        if (startPos <=0 ) {
                            startPos = 0;
                        }
                        endPos = startPos + numMediaDisplayed;
                    }
                    //console.log("Length:" + contentArr[memId].length);
                    //console.log('s:'+startPos + '|e:'+endPos);
                    subMemberArr = [];
                    subMemberArr[memId] = contentArr[memId].slice(startPos, endPos);
                    displayChildRow(memId, childId, subMemberArr);
                }
            }
        }

    });
    
    /*
     * Calls server for most recent social media and resets content array
     * for member
     */
    $(".navReload").click(function() {
        
        childId = $(this).data('childid');
        memId = childIdMemIdVisibleArr[childId];
        //reset contentArr
        contentArr[memId] = [];
        getJson(memId, 0, childId, false, false) 

    });
    
    //
    // Navigate members in parent layout
    //
       
    $(".childNext, .childPrev").click(function() {
       
        childId = $(this).data('childid');
        clickAction = $(this).attr('class');
        $("#circleG_" + childId).hide();
        
        //get member id that is currently visible for this child
        memId = childIdMemIdVisibleArr[childId];
        // get member's position in memNameArr
        currentPosition = 0;
        for(var i in memIdArr[childId]) {
            if (memId == memIdArr[childId][i]) {
                currentPosition = parseInt(i);
                break;
            }
        }
               
        // work on the current visible member

        // if size of box was exanded by adding a height style to parentHolder
        // remove any style added to parent holder
        $(this).closest('.parentCont').find('.parentHolder').css('height', '');
        // if shrinkLink is visible, trigger it so as to revert display to normal
        // size
        if ($('#shrinkLink_' + childId + '_1').is(":visible")) {
            $('#shrinkLink_' + childId + '_1').trigger('click');
        } else if ($('#shrinkLink_' + childId + '_2').is(":visible")) {
            $('#shrinkLink_' + childId + '_2').trigger('click');
        }

        // work on next or prev member

        if (clickAction == 'childNext') {
            // set childNext values
            if (typeof memNameArr[childId][parseInt(currentPosition + 1)] != 'undefined') {
                // next member to be made visible
                newIndex = parseInt(currentPosition + 1);
                // next member name in 'next' link to be made visible
                if (typeof memNameArr[childId][parseInt(newIndex + 1)] != 'undefined') {
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
            i = (currentPosition - 1);
            if (typeof memNameArr[childId][i] != 'undefined') {
                // prev member to be made visible
                newIndex = i;
                // prev member name in 'next' link to be made visible
                // NOTE 'prev' link is clicked, but there is no previous member name in previous link
                // only member name in 'next' link gets updated
                nextLinkIndex = currentPosition;
            } else {
                newIndex = memNameArr[childId].length - 1;
                nextLinkIndex = 0;
            } 
        }

        newMemberId = memIdArr[childId][newIndex];

        mngExpandShrinkLinks(newMemberId);

        // set the new member id to be visible
        childIdMemIdVisibleArr[childId] = newMemberId;

        if (typeof contentArr[newMemberId] == 'undefined' || contentArr[newMemberId].length == 0) {
            contentArr[newMemberId] = [];
            getJson(newMemberId, 0, childId, true, nextLinkIndex);
        } else {
            // name, avatar
            displayChildHeader(childId, newIndex);
            // social media image, text
            displayChildRow(newMemberId, childId, contentArr);
            nextMemberName = getMemberName(childId, nextLinkIndex);
            $('.next_child_id_' + childId).text(nextMemberName);
        }

    });
    
    /*
     * 
     * Display prev and next links to navigate members within a category
     */
    function mngChildNextLinks() {

        if ($(".childNext").length ) {

            for(var childId in memNameArr) {
                if (memNameArr[childId].length == 1) {
                    continue;
                }
                memberName = getMemberName(childId, 1);
                $(".next_child_id_" + childId).html(memberName);

            }   
        }
        
    }
    
    /*
     * For small screens, break name into less than 20 characters by
     * making use of any spaces in their name
     */
    function getMemberName(childId, nextLinkIndex) {
        
        width = $(window).width();
        memberName = memNameArr[childId][nextLinkIndex];
        if (width <= 360) {
            if (memberName.length > 18 ) {
                arr = memberName.split(' ');
                name = '';
                for(var i in arr) {
                    tmp = name + " " + arr[i];
                    if (tmp.length < 18) {
                        name = tmp;
                    } else {
                        break;
                    }
                }
                if (name.length < 5) {
                    memberName = memberName.substr(0, 18);
                } else {
                    memberName = name;
                }
            }
        }

        return memberName;
        
    }
   
   
    // MAIN display members in parent layout
    mngResize();
    mngChildNextLinks();
    var childIdMemIdVisibleArr = [];
    for(var memId in memIdChildIdArr) {
        
        childId = memIdChildIdArr[memId];

        // the ajax calls references these values to determine which social
        // media id to navigate to. 
        // Initialize them here
        memberIdSocialIdArr[memId] = {};
        memberIdSocialIdArr[memId].firstSocialMediaId = 0;       
        memberIdSocialIdArr[memId].lastSocialMediaId = 0;

        // some member's content hasn't been loaded on the main page at first
        if (typeof contentArr[memId] == 'undefined') {
            continue;
        }

        // work on members visible inside the child holder
        if (typeof childIdMemIdVisibleArr[childId] == 'undefined') {

            indexPosition = 0;
            
            // name, avatar
            displayChildHeader(childId, indexPosition);
            
            // store childId and member id that is visible for reference
            childIdMemIdVisibleArr[childId] = memId;
 
            displayChildRow(memId, childId, contentArr);
            
        }
        
    }
    
    /*
     * Display member name and avatar around row of content for member
     */
    function displayChildHeader(childId, indexPosition) {

        memName = getMemberName(childId, indexPosition);
        $("#rowCont_" + childId + " > .headerTextLeft > .memberName").text(memName);
        $("#rowCont_" + childId + " > .leftBar > .avatarHolder > .avatar").attr('src', memAvatarArr[childId][indexPosition]);
        
    }
    
    /*
     * Row of social media content for member
     */
    function displayChildRow(memId, childId, contentArr) {

        if (contentArr[memId].length == 0) {
            displayEndMsg(childId,1);
            return
        }     
        
        //get content for member
        var count = 0;
        for(var i in contentArr[memId]) {

            obj = contentArr[memId][i];

            displayChildBlock(obj, childId, count);
            
            if (i == 0){// && memberIdSocialIdArr[memId].firstSocialMediaId == 0) {
                memberIdSocialIdArr[memId].firstSocialMediaId = contentArr[memId][count]['id'];
            }

            count++;
            if (count >= numMediaDisplayed) {
                memberIdSocialIdArr[memId].lastSocialMediaId = contentArr[memId][count-1]['id'];
                break;
            } 
            
        }

        if (contentArr[memId].length == numMediaDisplayed - 1) {
            displayEndMsg(childId,2);
            return
        }  
         
    }
    
    /*
     * Display column of content for member
     */
    function displayChildBlock(obj, childId, count) {

        var idStr = childId + "_" + (count + 1);

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

        onLink = " on <a target='_blank' href='" + link + "'>" + obj['source'] + "&raquo;</a>";
        footerContent = obj['age'] + " " + onLink;
        $("#footerCont_" + idStr + " > .ageLink > .ageText").html(obj['age']);
        $("#footerCont_" + idStr + " > .ageLink > .onLink").html(onLink);

        mngExpandShrinkLinks(childId);

    }

    /*
     * Manage the showing/hiding of the exand and shrink links that reveal/hide
     * long social media text
     */
    function mngExpandShrinkLinks(id) {
        
        for (var i = 1; i <= numMediaDisplayed; i++) {
            
            idStr = id + '_' + i;
            
            // see if text overflows (parent div is set to overflow:hidden)
            if ($("#textCont_" + idStr).height() >  $("#textCont_" + idStr).parent().height()) {
                $("#expandLink_" + idStr).css('display', 'inline-block');
                $("#shrinkLink_" + idStr).css('display', 'none');
            } else {
                $("#expandLink_" + idStr).css('display', 'none');
                if ($("#textCont_" + idStr).parent().height() > originalTextContParentHeight) {
                    $("#shrinkLink_" + idStr).css('display', 'inline-block'); 
                } else {
                    $("#shrinkLink_" + idStr).css('display', 'none'); 
                }
            }
            
        }
        
    }

});