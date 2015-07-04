<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name') !!}
</div>
<div class="form-group">
    {!! Form::label('display_name', 'Display Name:') !!}
    {!! Form::text('display_name') !!}
</div>
<div class="form-group">
    {!! Form::label('slug', 'Slug:') !!}
    {!! Form::text('slug') !!}
</div>

<div class="form-group">
    {!! Form::label('', 'Is a parent?') !!}
    no: {!! Form::radio('is_a_parent', 0, ($is_a_parent?false:true)) !!}
    yes: {!! Form::radio('is_a_parent', 1, ($is_a_parent?true:false)) !!}
</div>

@if (count($selectedParentIdNameArr)) 
    {!! Form::label('Selected parents:') !!}
    <ul class='form-group'>
    @foreach($selectedParentIdNameArr as $selectedId => $name) 
        <li>{{ $name }} 
        &#183; Delete: {!! Form::checkbox('delete_parent_id[]', $selectedId) !!}
        {!! Form::hidden('parent_id[]', $selectedId) !!}
        </li>
    @endforeach
</ul> 
@endif

@if ( count($ddArr) )
    
    <div class="form-group">
    {!! Form::label('', 'Select a parent:') !!}
    {!! Form::select('parent_id[]', $ddArr, 0) !!}   
    </div>

@endif


<div class="form-group">
    {!! Form::submit('Submit Category') !!}
</div>

{!! Form::hidden('category_id', $id) !!}

<br>
{!! link_to_route('categories.index', '&laquo;Back to Categories') !!}
&nbsp; &#183; &nbsp; 
{!! link_to_route('categories.create', 'Create a Category&raquo;') !!}     