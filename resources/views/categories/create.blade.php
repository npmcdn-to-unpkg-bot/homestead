@extends('app')
 
@section('content')
    <h2>Create Category</h2>

    {!! Form::model($category, ['route' => ['categories.store', $category->slug]]) !!}
        @include('categories/partials/_form', [
            'is_a_parent' => $category->is_a_parent, 
            'id' => $category->id, 
            'selectedParentIdNameArr' => $selectedParentIdNameArr,
            'ddArr' => $ddArr
            ])
    {!! Form::close() !!}

@include('admin/partials/_footer')

<script src="/js/category_create.js"></script>    

@endsection