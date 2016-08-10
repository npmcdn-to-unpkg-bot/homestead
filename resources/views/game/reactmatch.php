<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>React Match</title>

    <script src="https://npmcdn.com/react@15.3.0/dist/react.js"></script>
    <script src="https://npmcdn.com/react-dom@15.3.0/dist/react-dom.js"></script>
    <script src="https://npmcdn.com/babel-core@5.8.38/browser.min.js"></script>
    <script src="https://npmcdn.com/jquery@3.1.0/dist/jquery.min.js"></script>
    <script src="/js/match.js"></script>
    <link href="/css/match.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="content"></div>

<script type="text/babel">

    var prevVisibleComponent = '';
    var curVisibleComponent = '';

    var Box = React.createClass({

        componentWillMount: function() {
            //console.log('componentWillMount');

        },
        componentDidMount: function() {
//            componentDidMount is called after the component is mounted and has a
//            DOM representation. This is often a place where you would attach generic DOM events.
            //console.log('componentDidMount');
        },
        componentWillReceiveProps: function() {
            //console.log('componentWillReceiveProps');
        },
        shouldComponentUpdate: function() {
            //console.log('shouldComponentUpdate');
            return true;
        },
        componentWillUpdate: function(nextProps, nextState) {
//        console.log('componentWillUpdate');
//        console.log(nextProps);
//        console.log(nextState);
        },
        componentDidUpdate: function() {
            //console.log('componentDidUpdate');

        },
        componentWillUnmount: function() {
            //console.log('componentWillUnmount');
        },
        // this does nothing
        getDefaultProps: function() {
            return {
                clickedIndex: 0,
                prevTarget: {}
            };
        },
        // If getInitialState does not return a clickedIndex value, a null value error is thrown upon instantiation
        getInitialState: function() {
            return {
                clickedIndex: 0
            };
        },

        handleOnClick: function(e) {

            if (globalObj.isValidClick(this.props.index) == false) {
                return;
            }

            this.setState({clickedIndex:this.props.index});

            // evaluate clicks
            var result = globalObj.eval(this.props);
            console.log("result:" + result);
            if (result == 'match') {
                //ReactDOM.findDOMNode(this.state.prevTarget).style.visibility = 'hidden';
            }

        },

        render: function() {

            // TODO - get second matched box to disappear. Both disappear after slight delay
            //      - possible solution: set listener in parent for the click on the box, not on the box itself, and the parent sets state
            // Have misses re-conceal after slight delay
            // Makes score and msg visible

            // set box to empty style if it is in completedArr
            var emptyBox = {};
            for(var i in globalObj.completedArr) {
                if (this.props.index == globalObj.completedArr[i]) {
                    emptyBox = {backgroundColor:'#cee3f8'};
                }
            }
            // if the box is not empty and box is clicked on, show the number/pattern
            var innerBoxStyle = {display:'none'};
            if (Object.keys(emptyBox).length == 0 && this.state.clickedIndex == this.props.index) {
                innerBoxStyle = {display:'block'};
            }

            return (
                <div style={emptyBox} className="Box" onClick={this.handleOnClick}>
                    <div style={innerBoxStyle} className="InnerBox">{this.props.displayJ}</div>
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