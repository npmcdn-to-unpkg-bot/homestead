<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>React Match</title>

    <script src="https://unpkg.com/react@15.3.0/dist/react.js"></script>
    <script src="https://unpkg.com/react-dom@15.3.0/dist/react-dom.js"></script>
    <script src="https://unpkg.com/babel-core@5.8.38/browser.min.js"></script>
    <script src="https://unpkg.com/jquery@3.1.0/dist/jquery.min.js"></script>
    <script src="/js/match.js"></script>
    <link href="/css/match.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="content"></div>

<script type="text/babel">

    var playAgain = React.createElement('a', {href: '?'}, 'New Game');

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
                        {this.props.footerMsg}
                    </h3>
                    <h3>
                        {playAgain}
                    </h3>
                </div>
            )
        }

    });

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
//            console.log('componentWillUpdate');
//            console.log(nextProps);
//            console.log(nextState);
        },
        componentDidUpdate: function() {
            //console.log('componentDidUpdate');

        },
        componentWillUnmount: function() {
            //console.log('componentWillUnmount');
        },

        getDefaultProps: function() {
            return {
                completed:0
            };
        },
        // If getInitialState does not return a clickedIndex value, a null value error is thrown upon instantiation
        getInitialState: function() {
            return {
                clickedIndex: 0
            };
        },

        handleOnClick: function() {


            if (globalObj.isValidClick(this.props.box.index) == false) {
                return;
            } else {
                // call the Game component parent
                this.props.onClick(this);
                this.setState({clickedIndex: this.props.box.index});
            }

        },

        render: function() {

            // set box to empty style if hide (it was matched) is true
            var emptyBox = {};
            if (this.props.box.completed == 1) {
                emptyBox = {backgroundColor:'#cee3f8',backgroundImage:"url('/img/100x100/completed.png')"};
            }

            // if the box is not hidden and box is clicked on, show the number/pattern
            var innerBoxStyle = {display:'none'};
            if (this.props.box.completed == 0 && this.state.clickedIndex == this.props.box.index) {
                innerBoxStyle = {display:'block'};
            }

            return (
                <div style={emptyBox} className="Box" onClick={this.handleOnClick}>
                    <div style={innerBoxStyle} className="InnerBox"><img src={this.props.box.img}/></div>
                </div>
            );
        }
    });

    var Boxes = React.createClass({

        render: function() {

            var boxNodes = this.props.data.map(function(box) {
                // attach onClick method in parent to each box with the box obj as the param
                var boundClick = () => this.props.clickFunc(box);
                // determine if box should be hidden or shown
                box.completed = 0;
                for(var i in globalObj.completedArr) {
                    if (box.index == globalObj.completedArr[i]) {
                        box.completed = 1;
                        break;
                    }
                }
                return (
                    <Box onClick={boundClick} box={box} key={box.index}></Box>
                );
            }, this);
            return (
                <div className="Boxes">
                    {boxNodes}
                </div>
            );
        }
    });

    var Game = React.createClass({

        getDefaultProps: function() {
            return {
                data: globalObj.getGrid()
            };
        },
        getInitialState: function() {
            return {
                renderKey: 1,
                footerMsg: ''
            };
        },
        handleClick: function(box) {

            // evaluate clicks
            var result = globalObj.eval(box);
            console.log("result:" + result);
            if (result == 'match' || result == 'miss') {
                // trigger re-rendering of Boxes
                setTimeout(function() {
                        this.setState({renderKey: Math.random()});
                        globalObj.resetClickCount();
                    }.bind(this),
                    750
                );
                if (globalObj.gameStatus == 'over') {
                    this.setState({footerMsg: 'Game Over'});
                }
            }

        },
        render: function() {
            return (
                <div className="Game">
                    <Header misses={globalObj.misses} matches={globalObj.matches} score={globalObj.getScore()} />
                    <Boxes
                        clickFunc={this.handleClick}
                        data={this.props.data}
                        key={this.state.renderKey}/>
                    <Footer footerMsg={this.state.footerMsg}/>
                </div>
            );
        }
    });

    ReactDOM.render(
        <Game />,
        document.getElementById('content')
    );

</script>
</body>
</html>