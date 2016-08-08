<!DOCTYPE html>
<html>
<head>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>

    <style>
        .box{
            border:1px solid #cccccc;
            width:100px;
            height:100px;
            background-color:#dddddd;
            float:left;
            font-size:60px;
            font-weight:bold;
            text-align:center;
            vertical-align:middle;
        }
    </style>
</head>

<body>

<h3>Matches: <span id="matches">0</span> Misses: <span id="misses">0</span> Score:<span id="score">0</span></h3>

<div id='cont' style='height:408px;width:408px;border:1px solid black;'>
</div>
<h3 id="msg" style=""></h3>

<script>

    function shuffle(array) {
        var currentIndex = array.length, temporaryValue, randomIndex;

        // While there remain elements to shuffle...
        while (0 !== currentIndex) {

            // Pick a remaining element...
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;

            // And swap it with the current element.
            temporaryValue = array[currentIndex];
            array[currentIndex] = array[randomIndex];
            array[randomIndex] = temporaryValue;
        }

        return array;
    }

    numArr = [];
    for(var j = 1; j <= 8; j++) {
        numArr.push(j);
    }

    for(var i = 1; i <= 2; i++) {
        numArr = shuffle(numArr);
        for(var x in numArr) {
            j = numArr[x];
            id = "ij_" + i + "_" + j;
            str = "<a id='href_" + id + "' href='javascript:void(0);'>";
            str+= "<div id='" + id + "' data-j='" + j + "' data-i='" + i + "' class='box'>";
            //str+= 'ij_' + i + '_' + j;
            str+= '&nbsp; </div>';
            str+= '</a>';
            $("#cont").append(str);
        }
    }

    var score = 0;
    visibleIArr = [];
    visibleJArr = [];
    visibleIdArr = [];
    completedArr = [];
    var matches = 0;
    var misses = 0;

    $(".box").click(function() {

        i = $(this).data('i');
        j = $(this).data('j');
        id = $(this).attr('id');

        // don't compute already clicks on already completed squares
        for(var key in completedArr) {
            if (completedArr[key] == id) {
                return;
            }
        }

        $("#" + id).html(id);

        visibleIArr.push(i);
        visibleJArr.push(j);
        visibleIdArr.push(id);

        // Display the hidden value in the square
        tmp = id.split("_").pop();
        $(this).html(tmp);

        if (visibleJArr.length == 2) {
            idOne = visibleIdArr[0];
            idTwo = visibleIdArr[1];
            if (visibleJArr[1] == visibleJArr[0]) {
                completedArr.push(idOne);
                completedArr.push(idTwo);
                //console.log("match: " + visibleJArr[1] + "|" + visibleJArr[0]);
                $("#href_" + idOne).contents().unwrap();
                $("#href_" + idTwo).contents().unwrap();
                $("#" + idOne).animate({backgroundColor: '#ffffff'}, 1000);
                $("#" + idTwo).animate({backgroundColor: '#ffffff'}, 1000);
                matches++;
            } else {
                misses++;
                //console.log("no match: " + j + "|" + visibleJArr[0]);
            }
            setTimeout(function(){$("#" + idOne).html('');}, 1000);
            setTimeout(function(){$("#" + idTwo).html('');}, 1000);
            visibleJArr = [];
            visibleIdArr = [];
            $("#misses").text(misses);
            $("#matches").text(matches);
            if (matches ==0 || misses ==0 ) {
                score = 0;
            } else {
                score = parseInt((matches / misses) * 100) + '%';
            }
            //console.log("score:" + score + "|" + misses + "|" + matches);
            $("#score").text(score);
        }

        if (numArr.length == completedArr.length/2) {
            $("#msg").html("Game Over. <a href=''>Play again.</a>");
        }

    });

</script>


</body>

</html>