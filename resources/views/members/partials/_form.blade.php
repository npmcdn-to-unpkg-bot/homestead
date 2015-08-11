<div class='left_col'>

<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name') !!}
</div>
    
    <!--
<div class="form-group">
    {!! Form::label('first_name', 'First Name:') !!}
    {!! Form::text('first_name') !!}
</div>
<div class="form-group">
    {!! Form::label('last_name', 'Last Name:') !!}
    {!! Form::text('last_name') !!}
</div>
    -->
<div class="form-group">
    {!! Form::label('avatar', 'Avatar:') !!}
    {!! Form::text('avatar') !!}
    <?php
    if ($memberObj->avatar != '') {
        echo "<img src='" . $memberObj->avatar . "'>";
    }
    ?>
</div>


{!! Form::label('Social Media Ids:') !!}
<ul class='form-group'>
    <br>
    <?php
    foreach($memberSocialIdArr as $siteKey => $arr) {

        $labelName = $arr['name'];
        if ($arr['name'] == 'Twitter' && $arr['memberSocialId'] != '') {
            $labelName = '<a target="_blank" href="https://twitter.com/' . $arr['memberSocialId'] . '">Twitter</a>';
        } else if ($arr['name'] == 'Instagram' && $arr['memberSocialId'] != '') {
            $labelName = '<a target="_blank" href="https://instagram.com/' . $arr['memberSocialId'] . '">Instagram</a>';
        }
    ?>
    
        {!! Html::decode(Form::label('site', $labelName . ":", array('class' => 'site_label'))) !!}
        {!! Form::text("site[$siteKey][id]", $arr['memberSocialId']) !!}
        
        on: {!! Form::radio("site[$siteKey][disabled]", 0, ($arr['disabled']?false:true)) !!}
        off: {!! Form::radio("site[$siteKey][disabled]", 1, ($arr['disabled']?true:false)) !!}
        
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
<a href='javascript:void(0);' onclick='history.go(-1);'>&laquo;Back</a>
&nbsp; &#183; &nbsp; 
<a href='/members/uncategorized'>Uncategorized Members</a>
&nbsp; &#183; &nbsp;
{!! link_to_route('members.create', 'Add a Member&raquo;') !!}

</div>

<div class='right_col' style='margin-top:0px;margin-left:500px;'>

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

<script src='/js/admin.js'></script>