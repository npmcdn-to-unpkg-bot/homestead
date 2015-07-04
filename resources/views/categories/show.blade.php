@extends('app')

@section('content')
 
    @if ( !$category->count() )
        Category not found
    @else
    <h2>{{ $category->name }}</h2>
        <ul>
                <li>
                    {!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('categories.destroy', $category->slug))) !!}
                        <a href="{{ route('categories.show', [$category->slug]) }}">{{ $category->display_name }}</a>
                        (
                            {!! link_to_route('categories.edit', 'Edit', array($category->slug), array('class' => 'btn btn-info')) !!},
 
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger')) !!}
                        )
                    {!! Form::close() !!}
                </li>
        </ul>
    @endif
 
    <p>
        {!! link_to_route('categories.index', 'Back to Categories') !!} |
        {!! link_to_route('categories.create', 'Create Category', '') !!}
    </p>
@endsection