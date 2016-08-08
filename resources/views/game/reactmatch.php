<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>React Tutorial</title>
    <!-- Not present in the tutorial. Just for basic styling. -->
    <script src="https://npmcdn.com/react@15.3.0/dist/react.js"></script>
    <script src="https://npmcdn.com/react-dom@15.3.0/dist/react-dom.js"></script>
    <script src="https://npmcdn.com/babel-core@5.8.38/browser.min.js"></script>
    <script src="https://npmcdn.com/jquery@3.1.0/dist/jquery.min.js"></script>

    <link href="/css/match.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="content"></div>

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

// Build the grid
// Set an array with a sequential range of numbers
var gridArr = [];
var numArr = [];
for(var j = 1; j <= 8; j++) {
    numArr.push(j);
}

// Loop over the array twice, randomizing the order each time. Store the randomized array
var index = 0;
for(var i = 1; i <= 2; i++) {
    numArr = shuffle(numArr);
    for(var x in numArr) {
        ++index;
        j = numArr[x];
        obj = {
            index: index,
            indexKey: index,
            i: i,
            j:j,
            displayJ:j
        };
        gridArr.push(obj);
    }
}

</script>

<script type="text/babel">

var score = 0;
var visibleJArr = [];
var visibleIndexArr = [];
var completedArr = [];
var matches = 0;
var misses = 0;
var visibleComponent = '';
var tmp = '';//

var Box = React.createClass({

    componentWillMount: function() {
        console.log('componentWillMount');
        console.log('this.state', this.state);
    },
    componentDidMount: function() {
        console.log('componentDidMount');
        console.log('this.state', this.state);
        console.log('this.refs', this.refs);
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
        console.log('this.state', this.state);
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
    getInitialState: function() {
        console.log('getInitialState');
        return {data: []};
    },
    handleOnClick: function(e) {

        e.currentTarget.children[0].style.display='block';

        // don't compute clicks on already completed squares
        for(var key in completedArr) {
            if (completedArr[key] == this.props.index) {
                return;
            }
        }

        // if they've clicked on the same square twice
        if (visibleIndexArr.length == 1 && this.props.index == visibleIndexArr[0]) {
            return;
        }

        visibleJArr.push(this.props.j);
        visibleIndexArr.push(this.props.index);
        if (visibleJArr.length == 2) {
            if (visibleJArr[0] == visibleJArr[1]) {
                matches++;
                completedArr.push(index);
                this.setState({matches:completedArr.length})
            } else {
                misses++;
                this.setState({misses:misses});
            }
            visibleJArr = new Array();
            visibleIndexArr = new Array();
            $("#misses").text(misses);
            $("#matches").text(matches);
            if (matches ==0 || misses ==0 ) {
                score = 0;
            } else {
                score = parseInt((matches / misses) * 100) + '%';
            }
            //console.log("score:" + score + "|" + misses + "|" + matches);
            $("#score").text(score);

            if (numArr.length == completedArr.length/2) {
                $("#msg").html("Game Over. <a href=''>Play again.</a>");
            }

            //this.setState({displayJ:'asf'});
            //setTimeout(function(){ReactDOM.findDOMNode(visibleComponent).style.display='none';}, 1000);
            //setTimeout(function(){ReactDOM.findDOMNode(visibleComponent).style.display='none';}, 1000);
            //var tmp = e;
            //setTimeout(function(tmp){tmp.currentTarget.children[0].style.display='none'}, 1000);
            //e.currentTarget.children[0].style.display='none';
            //var tmp = this.props.index;
            //var tmp = this.props.index;
            //console.log(this.props.index);
            //console.log(document.getElementsByClassName('Boxes')[0].children[(tmp -1 )]);
            //setTimeout(function(){document.getElementsByClassName('Boxes')[0].children[tmp-1].style.display='none'}, 1000);
        } else {
            visibleComponent = e.currentTarget.children[0];
        }

    },

    render: function() {

        return (
            <div className="Box" onClick={this.handleOnClick}>
            <div className="InnerBox">{this.props.displayJ}</div>
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

    render: function() {
        return (
            <div className="Header">
                <h3>
                    Matches: <span id="matches">{this.props.matches}</span> &nbsp;
                    Misses: <span id="misses">{this.props.misses}</span> &nbsp;
                    Score:<span id="score">0</span>
                </h3>
            </div>
        )
    }

});

var Footer = React.createClass({

    render: function() {
        return (
            <div className="Footer">
                <h3 id="msg">

                </h3>
            </div>
        )
    }

});

var Game = React.createClass({
    render: function() {
        return (
            <div className="Game">
                <Header />
                <Container data={this.props.data} />
                <Footer refs="footer"/>
            </div>

         );
     }
});

ReactDOM.render(
    <Game data={gridArr} />,
    document.getElementById('content')
);


</script>
</body>
</html>