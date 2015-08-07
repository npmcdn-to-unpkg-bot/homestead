@extends('app')

@section('content')

    <div class='left_col' style='margin-right:60px;'>

      <div style='width:340px;text-align:center;margin-bottom:10px;'>

        <?php
        
        // category navigation
        renderCategoryPath($catPathArr);

        // page number navigation
        if ($prev !== false) {
            echo "<a style='float:left;' href='" . Request::url() . "/?next=$prev'>&laquo; Prev $prev</a> ";
        }
        
        if ($next) {
            echo "<a style='float:right;' href='" . Request::url() . "/?next=$next'>Next $next &raquo;</a>";
        }
        
        ?>
    
    </div>

    <br>        
        
    @if ( !$membersObj->count() )
        No members 
    @else
    
        <ul class='list-unstyled'>
            @foreach( $membersObj as $obj )
                <li class='cat_row'>
                    {!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('members.destroy', $obj->id))) !!}
                        <div class='category_label'>
                            {{ $obj->name }} 
                            
                        </div>
                            {!! link_to_route('members.edit', 'Edit', array($obj->id), array('class' => 'btn btn-info')) !!} &nbsp;   
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger')) !!}
                    {!! Form::close() !!}
                </li>
            @endforeach
        </ul>
    @endif

    </div>

    <div class='right_col' style='float:none;margin-top:-20px;'>
        
       <h2>Browse by Category</h2>
       &#183; <a href='/members/nochild'>Browse no child</a>
       <br>
       &#183; <a href='/members/uncategorized'>Browse uncategorized</a>
       <br><br>
       <?php 
        renderTree($parentChildArr, $categoriesArr, 'members');
       ?> 
    </div> 
    
<script src="/js/form_index.js"></script>    

@endsection
