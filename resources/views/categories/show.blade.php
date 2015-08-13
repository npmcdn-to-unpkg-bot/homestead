@extends('app')

@section('content')

@if ( empty($parentCatArr) )
    No categories 
@else
    Parent categories can be sorted below. 
    <br><br>
    <ul id='sortable'>
        @foreach( $parentCatArr as $id => $name )
            <li id="cat_' . {{$id}}" class="ui-state-default catBox">
                <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                <div>{{ $name }}</div>
            </li>
        @endforeach
    </ul>
@endif


<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src='/js/admin.js'></script>

<style>

/* sort category styles */
#sortable { list-style-type: none;margin-left:-35px;}
#sortable li span { position: absolute; margin-left: -1.3em; }
#sortable li {margin:2px;padding:4px;width:50%;height:30px;}
.catBox{
	float:left;
	padding:4px;
	width:200px;
}

</style>

@endsection
