@extends('app')

@section('content')

    
    @if ( !$membersObj->count() )
        You have no members 
    @else
        <ul class='list-unstyled'>
            @foreach( $membersObj as $obj )
                <li class='cat_row'>
                    {!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('members.destroy', $obj->id))) !!}
                        <div class='category_label'>{{ $obj->last_name }}, {{$obj->first_name}}</div>
                            {!! link_to_route('members.edit', 'Edit', array($obj->id), array('class' => 'btn btn-info')) !!} &nbsp;   
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger')) !!}
                    {!! Form::close() !!}
                </li>
            @endforeach
        </ul>
    @endif
 
    
   
    </div>
    
<script src="/js/form_index.js"></script>    

@endsection
