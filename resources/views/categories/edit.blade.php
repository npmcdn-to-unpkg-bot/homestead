@extends('app')
 
@section('content')
    <h2>Edit Category</h2>
 
    {!! Form::model($category, ['method' => 'PATCH', 'route' => ['categories.update', $category->slug]]) !!}
        @include('categories/partials/_form', [
            'is_a_parent' => $category->is_a_parent, 
            'id' => $category->id, 
            'selectedParentIdNameArr' => $selectedParentIdNameArr,
            'ddArr' => $ddArr
            ])
    {!! Form::close() !!}

@include('admin/partials/_footer')

@endsection