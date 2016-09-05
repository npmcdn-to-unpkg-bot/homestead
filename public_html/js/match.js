var globalObj = {

    gridArr: [], // array of objects representing the game board of squares aka boxes aka tiles
    numPieces: 8,// two pairs of numPieces, 8 numPieces = 16 boxes
    score: 0,
    matches: 0,
    misses: 0,
    visibleJArr: [],
    completedArr: [],
    visibleIndexArr: [],//1 through numPieces
    gameStatus: 'running',// or 'over'
    clickCount:0,

    getScore: function()
    {
        if (this.matches == 0 || this.misses == 0 ) {
            score = 0;
        } else {
            score = parseInt((this.matches / this.misses) * 100) + '%';
        }
        return score;
    },
    isValidClick: function(index)
    {

        // don't compute clicks on already completed squares
        for(var key in this.completedArr) {
            if (typeof this.completedArr[key] != 'undefined' && this.completedArr[key] == index) {
                return false;
            }
        }

        // if they've clicked on the same square twice
        if (this.visibleIndexArr.length == 1 && index == this.visibleIndexArr[0]) {
            return false;
        }

        // the visibleArr length gets set after this isValidClick() function, so use this.clickCount instead
        this.clickCount++;
        console.log("clickCount: " + this.clickCount);
        if (this.clickCount > 2) {
            return false;
        }

        return true;

    },
    resetClickCount: function()
    {
        this.clickCount = 0;
    },
    eval: function(props)
    {
        this.visibleJArr.push(props.j);
        this.visibleIndexArr.push(props.index);
        // If we have two clicked boxes
        if (this.visibleJArr.length == 2) {
            if (this.visibleJArr[0] == this.visibleJArr[1]) {
                this.matches++;
                // get the index for each box and store it in completedArr
                for(var i in this.gridArr) {
                    if (this.gridArr[i].j == this.visibleJArr[0]) {
                        this.completedArr.push(this.gridArr[i].index);
                    } else if (this.gridArr[i] == this.visibleJArr[1]) {
                        this.completedArr.push(this.gridArr[i].index);
                    }
                }
                var result = 'match';
            } else {
                this.misses++;
                var result = 'miss';
            }

            this.visibleJArr = new Array();
            this.visibleIndexArr = new Array();

            if (this.numPieces == this.completedArr.length / 2) {
                this.msg = "Game Over. ";
                this.gameStatus = "over";
            }

            return result;
        }

        return 'oneclicked';
    },
    // shuffle function via stackoverflow
    shuffle: function(array) {

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
    },
    getGrid:function () {

        // Build the grid
        // Set an array with a sequential range of numbers
        var numArr = [];
        for(var j = 1; j <= this.numPieces; j++) {
            numArr.push(j);
        }

        // Loop over the array twice, randomizing the order each time. Store the randomized array
        var index = 0;
        for(var i = 1; i <= 2; i++) {
            numArr = this.shuffle(numArr);
            for(var x in numArr) {
                ++index;
                j = numArr[x];
                var obj = {
                    index: index,
                    i: i,
                    j:j,
                    displayJ: j,
                    img: '/img/100x100/' + j + '.png'
                };
                this.gridArr.push(obj);
            }
        }

        return this.gridArr;

    }

};