@extends('app')

@section('content')

    <p>
        {!! link_to_route('categories.create', 'Create Category') !!}
    </p>
    
    <div class='left_col'>
    
    <h2> &nbsp; Categories</h2>
    <br>
 
    @if ( !$categoriesObj->count() )
        You have no categories 
    @else
        <ul class='list-unstyled'>
            @foreach( $categoriesObj as $obj )
                <li class='cat_row'>
                    {!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('categories.destroy', $obj->slug))) !!}
                        <div class='category_label'>{{ $obj->name }}</div>
                            {!! link_to_route('categories.edit', 'Edit', array($obj->slug), array('class' => 'btn btn-info')) !!} &nbsp;   
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger')) !!}
                    {!! Form::close() !!}
                </li>
            @endforeach
        </ul>
    @endif
 
    
   
    </div>
    
    <div class='right_col'>
       <h2>Hierarchy</h2>
       <?php 
       foreach($parentChildArr as $itemArr) {
            $route = '/socialmedia/' . $categoriesArr[$itemArr['child_id']]['slug']; 
            echo renderItem($itemArr, $categoriesArr, $route);
       }
       ?> 
    </div> 
    
<script src="/js/form_index.js"></script>    

@endsection
