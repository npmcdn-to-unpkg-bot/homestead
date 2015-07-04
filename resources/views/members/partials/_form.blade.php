<div class='left_col'>

<div class="form-group">
    {!! Form::label('first_name', 'First Name:') !!}
    {!! Form::text('first_name') !!}
</div>
<div class="form-group">
    {!! Form::label('last_name', 'Last Name:') !!}
    {!! Form::text('last_name') !!}
</div>

{!! Form::label('Social Media Ids:') !!}
<ul class='form-group'>
    <br>
    <?php
    foreach($memberSocialIdArr as $siteKey => $arr) {
    ?>
        {!! Form::label('site', $arr['name'] . ":", array('class' => 'site_label')) !!}
        {!! Form::text("site[$siteKey]", $arr['memberSocialId']) !!}
        <br>
    <?php
    }
    ?> 
</ul> 


<div class="form-group">
    {!! Form::submit('Submit') !!}
</div>

{!! Form::hidden('member_id', $memberObj->id) !!}


<br>
{!! link_to_route('members.index', '&laquo;Back to Members') !!}
&nbsp; &#183; &nbsp; 
{!! link_to_route('members.create', 'Add a Member&raquo;') !!}

</div>

<div class='right_col'>

<?php 
foreach($parentChildArr as $itemArr) {
    echo renderCheckboxItem($itemArr, $categoriesArr, $memberCategoryIdArr);
}
?> 

</div>


<style>
    .site_label{
        width:80px;
    } 
</style>