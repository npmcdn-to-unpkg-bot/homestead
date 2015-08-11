@extends('app')
 
@section('content')

    <h2>Add a Member</h2>

    {!! Form::model($memberObj, ['route' => ['members.store', $memberObj->id]]) !!}
        @include('members/partials/_form', [
            'memberSocialIdArr' => $memberSocialIdArr,
            'parentChildArr' => $parentChildArr,
            'categoriesArr' => $categoriesArr,
            'memberCategoryIdArr' => $memberCategoryIdArr
            ])
    {!! Form::close() !!}
@include('admin/partials/_footer')
@endsection