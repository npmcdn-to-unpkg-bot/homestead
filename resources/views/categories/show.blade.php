@extends('app')

@section('content')

<?php
$encrypter = app('Illuminate\Encryption\Encrypter');
$encrypted_token = $encrypter->encrypt(csrf_token());
?>

<form>
    
<input id="token" type="hidden" value="<?php echo $encrypted_token; ?>">

@if ( empty($parentCatArr) )
    No categories 
@else
    Parent categories can be sorted below. 
    <br><br>
    <ul id='sortable'>
        @foreach( $parentCatArr as $id => $name )
            <li id="cat_{{$id}}" class="ui-state-default catBox">
                <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                <div>{{ $name }}</div>
            </li>
        @endforeach
    </ul>
@endif

</form>



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


@include('admin/partials/_footer')

@endsection
