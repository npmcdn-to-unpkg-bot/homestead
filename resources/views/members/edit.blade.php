@extends('app')
 
@section('content')
    <h2>Edit Member</h2>
    <hr>
 
    {!! Form::model($memberObj, ['method' => 'PATCH', 'route' => ['members.update', $memberObj->id]]) !!}
        @include('members/partials/_form', [
            'memberObj' => $memberObj, 
            'memberSocialIdArr' => $memberSocialIdArr,
            'parentChildArr' => $parentChildArr,
            'categoriesArr' => $categoriesArr,
            'memberCategoryIdArr' => $memberCategoryIdArr
            ])
    {!! Form::close() !!}

@include('admin/partials/_footer')

@endsection