@extends('app')
 
@section('content')
    <h2>Edit Member</h2>
    <hr>

    {!! Form::model($memberEnt, ['method' => 'PATCH', 'route' => ['members.update', $memberEnt->id]]) !!}
        @include('members/partials/_form', [
            'memberEnt' => $memberEnt, 
            'memberSocialIdArr' => $memberSocialIdArr,
            'parentChildArr' => $parentChildArr,
            'categoriesArr' => $categoriesArr,
            'memberCategoryIdArr' => $memberCategoryIdArr
            ])
    {!! Form::close() !!}

@include('admin/partials/_footer')

@endsection