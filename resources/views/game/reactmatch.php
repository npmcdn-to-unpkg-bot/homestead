<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>React Match</title>

    <script src="https://npmcdn.com/react@15.3.0/dist/react.js"></script>
    <script src="https://npmcdn.com/react-dom@15.3.0/dist/react-dom.js"></script>
    <script src="https://npmcdn.com/babel-core@5.8.38/browser.min.js"></script>
    <script src="https://npmcdn.com/jquery@3.1.0/dist/jquery.min.js"></script>

    <link href="/css/match.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="content"></div>

<script>

    var prevVisibleComponent = '';
    var curVisibleComponent = '';

</script>

<script type="text/babel">

    var globalObj = {

        numPieces: 8,
        score: 0,
        matches: 0,
        misses: 0,
        visibleJArr: [],
        completedArr: [],
        visibleIndexArr: [],
        completedArr: [],

        getScore: function()
        {
            if (this.matches == 0 || this.misses == 0 ) {
                this.score = 0;
            } else {
                this.score = parseInt((this.matches / this.misses) * 100) + '%';
            }
        },
        isValidClick: function(index)
        {
            // don't compute clicks on already completed squares
            for(var key in this.completedArr) {
                if (typeof this.completedArr[key] != 'undefined' && this.completedArr[key] == this.props.index) {
                    return false;
                }
            }

            // if they've clicked on the same square twice
            if (this.visibleIndexArr.length == 1 && index == this.visibleIndexArr[0]) {
                return false;
            }

            return true;
        },
        eval: function(props)
        {
            this.visibleJArr.push(props.j);
            this.visibleIndexArr.push(props.index);
            if (this.visibleJArr.length == 2) {
                if (this.visibleJArr[0] == this.visibleJArr[1]) {
                    this.matches++;
                    this.completedArr.push(props.index);
                    var result = 'match';
                } else {
                    this.misses++;
                    var result = 'miss';
                }

                this.visibleJArr = new Array();
                this.visibleIndexArr = new Array();

                if (this.numPieces == this.completedArr.length / 2) {
                    this.msg = "Game Over. <a href=''>Play again.</a>";
                }

                return result;
            }

            return 'oneclicked';
        },
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
            var gridArr = [];
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
                        indexKey: index,
                        i: i,
                        j:j,
                        displayJ:j
                    };
                    gridArr.push(obj);
                }
            }

            return gridArr;

        }

    };

    var Box = React.createClass({

        componentWillMount: function() {
            console.log('componentWillMount');
        },
        componentDidMount: function() {
//            componentDidMount is called after the component is mounted and has a
//            DOM representation. This is often a place where you would attach generic DOM events.
            console.log('componentDidMount');
        },
        componentWillReceiveProps: function() {
            console.log('componentWillReceiveProps');
        },
        shouldComponentUpdate: function() {
            console.log('shouldComponentUpdate');
            return true;
        },
        componentWillUpdate: function() {
            console.log('componentWillUpdate');
        },
        componentDidUpdate: function() {
            console.log('componentDidUpdate');

        },
        componentWillUnmount: function() {
            console.log('componentWillUnmount');
        },
        getDefaultProps: function() {
            console.log('getDefaultProps');
        },

        handleOnClick: function(e) {

            if (globalObj.isValidClick(this.props.index) == false) {
                return;
            }

            // set current target
            curVisibleComponent = e.currentTarget.children[0];
            curVisibleComponent.style.display = 'block';

            // evaluate clicks
            var result = globalObj.eval(this.props);
            if (result == 'match') {
                //remove boxes
                setTimeout(function () {
                    ReactDOM.findDOMNode(curVisibleComponent).style.display = 'none';
                }, 1000);
                setTimeout(function () {
                    ReactDOM.findDOMNode(prevVisibleComponent).style.display = 'none';
                }, 1000);
console.log('match');
            } else if (result == 'miss') {
                // conceal number
console.log('miss');
            } else {
                prevVisibleComponent = e.currentTarget.children[0];
            }

        },

        render: function() {

            var style = 'display:none';
            if (this.props.clickedIndex == this.props.index) {
                style = 'display:block';
            }

            return (
                <div className="Box" onClick={this.handleOnClick}>
                    <div style={{style}} className="InnerBox">{this.props.displayJ}</div>
                </div>
            );
        }
    });

    var Boxes = React.createClass({
        render: function() {
            var boxNodes = this.props.data.map(function(box) {
                return (
                    <Box displayJ={box.displayJ} j={box.j} i={box.i} key={box.indexKey} index={box.index}>
                    </Box>
                );
            });
            return (
                <div className="Boxes">
                    {boxNodes}
                </div>
            );
        }
    });

    var Container = React.createClass({
        render: function() {
            return (
                <div id="container" className="Container">
                    <Boxes data={this.props.data} />
                </div>
            );
        }
    });

    var Header = React.createClass({
        // does nothing, i have to pass in values in Game component init
//        getInitialState: function() {
//            console.log('getInitialState');
//            //return {data:[]};
//            return {misses: 0, matches:0};
//        },
//        componentDidMount: function() {
//            console.log(' header componentDidMount');
//            window.addEventListener('click', this.handleHeaderData);
//
//        },
//        componentWillUnmount: function() {
//            window.removeEventListener('click', this.handleHeaderData);
//        },
//        handleHeaderData: function(e) {
//
//            this.setState({});// triggers a re-rendering, don't need to set anything
//
//        },

        render: function() {
console.log("header", globalObj);

            return (
                <div ref="Header" className="Header">
                    <h3>
                        Matches: {this.props.matches} &nbsp;
                        Misses: {this.props.misses} &nbsp;
                        Score: {this.props.score}
                    </h3>
                </div>
            )
        }

    });

    var Footer = React.createClass({

        render: function() {
            return (
                <div className="Footer">
                    <h3>
                        {this.props.msg}
                    </h3>
                </div>
            )
        }

    });

    var Game = React.createClass({

        render: function() {
            return (
                <div className="Game">
                    <Header misses={globalObj.misses} matches={globalObj.matches} score={globalObj.getScore()} />
                    <Container data={this.props.data} />
                    <Footer msg={globalObj.msg} />
                </div>
            );
        }
    });

    ReactDOM.render(
        <Game data={globalObj.getGrid()} />,
        document.getElementById('content')
    );

</script>
</body>
</html>