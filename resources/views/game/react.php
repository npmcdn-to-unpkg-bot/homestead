<!DOCTYPE html>
<html>
<head>
    <script src="https://npmcdn.com/react@15.3.0/dist/react.js"></script>
    <script src="https://npmcdn.com/react-dom@15.3.0/dist/react-dom.js"></script>
    <script src="https://npmcdn.com/babel-core@5.8.38/browser.min.js"></script>
    <meta charset="utf-8">
    <title>JS Bin</title>
</head>
<body>

<div id="content"></div>

<style>
.box{
    border:1px solid black;width:100px;height:100px;
    }
</style>
<script type="text/babel">

    var Parent = React.createClass({

        getInitialState: function() {
            return {
                value: 'foo'
            }
        },

        changeHandler: function(value) {
            this.setState({
                value: value
            });
        },

        render: function() {
            return (
                <div>
                    <Child value={this.state.value} onChange={this.changeHandler} />
                    <span>{this.state.value}</span>
                </div>
            );
        }
    });

    var Child = React.createClass({
        propTypes: {
            value:      React.PropTypes.string,
            onChange:   React.PropTypes.func
        },
        getDefaultProps: function() {
            return {
                value: ''
            };
        },
        changeHandler: function(e) {
            if (typeof this.props.onChange === 'function') {
                this.props.onChange(e.target.value);
            }
        },
        render: function() {
            return (
                <input type="text" value={this.props.value} onChange={this.changeHandler} />
            );
        }
    });
//
//    var Todo = React.createClass({
//        render: function() {
//            return <div onClick={this.props.onClick}>{this.props.title}</div>;
//        },
//
//        //this component will be accessed by the parent through the `ref` attribute
//        animate: function() {
//            console.log('Pretend %s is animating', this.props.title);
//        }
//    });
//
//    var Todos = React.createClass({
//        getInitialState: function() {
//            return {items: ['Apple', 'Banana', 'Cranberry']};
//        },
//
//        handleClick: function(index) {
//            var items = this.state.items.filter(function(item, i) {
//                return index !== i;
//            });
//            this.setState({items: items}, function() {
//                if (items.length === 1) {
//                    this.refs.item0.animate();
//                }
//            }.bind(this));
//        },
//
//        render: function() {
//            return (
//                <div>
                    {this.state.items.map(function(item, i) {
                        var boundClick = this.handleClick.bind(this, i);
                        return (
                            <Todo onClick={boundClick} key={i} title={item} ref={'item' + i} />
                        );
                    }, this)}
//                </div>
//            );
//        }
//    });

    ReactDOM.render(<Parent />, document.getElementById('content'));
//
//var Child = React.createClass({
//    render: function() {
//        return (<div onClick={this.props.onClick} className="box">{this.props.index}</div>);
//    }
//});
//
//var Children = React.createClass({
//    handleClick: function(index) {
//        console.log("asdf");
////        var items = this.state.items.filter(function(item, i) {
////            return index !== i;
////        });
////        this.setState({items: items}, function() {
////            if (items.length === 1) {
////                this.refs.item0.animate();
////            }
////        }.bind(this));
//    },
//    render: function() {
//        var childNodes = this.props.data.map(function(child, i) {
//            var boundClick = this.handleClick.bind(this, i);
//            return (
//                <Child onClick={boundClick} key={child.index} index={child.index} />
//            );
//        });
//        return (
//            <div className="Children">
//                {childNodes}
//            </div>
//        );
//    }
//});
//
//var Parent = React.createClass({
//
//    render: function() {
//        return (
//            <div>
//                <Children data={this.props.data} />
//            </div>
//        );
//    }
//});
//var obj = [];
//obj.push({index:1});
//obj.push({index:2});
//ReactDOM.render(
//    <Parent data={obj} />,
//    document.getElementById('content')
//);


</script>
</body>
</html>