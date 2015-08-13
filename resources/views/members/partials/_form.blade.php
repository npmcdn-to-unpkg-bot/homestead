<div class='left_col'>

<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name') !!}
    <br><br>
    {!! Form::label('display_name', 'Display Name:') !!}
    {!! Form::text('display_name') !!}
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

<div class="form-group">
    {!! Form::label('avatar', 'Avatar:') !!}
    {!! Form::text('avatar') !!}
    <?php
    if ($memberEnt->avatar != '') {
        echo "<img src='" . $memberEnt->avatar . "'>";
    }
    ?>
</div>
    -->

{!! Form::label('Social Media:') !!}
<ul class='form-group'>
    <br>
    
    <?php
    
    foreach($memberSocialIdArr as $siteKey => $arr) {

        $labelName = $arr['name'];
        if ($arr['memberSocialId'] != '') {
            $site = strtolower($arr['name']) . '.com';
            $labelName = '<a target="_blank" href="https://' . $site . '/' . $arr['memberSocialId'] . '">';
            $labelName.= $arr['name'] . '</a>';
        } 
        
    ?>
    
        {!! Html::decode(Form::label('site', $labelName . ":", array('class' => 'site_label'))) !!}
        {!! Form::text("site[$siteKey][id]", $arr['memberSocialId']) !!}
        

        on: {!! Form::radio("site[$siteKey][disabled]", 0, ($arr['disabled']?false:true)) !!}
        off: {!! Form::radio("site[$siteKey][disabled]", 1, ($arr['disabled']?true:false)) !!}
        


        
        <?php
        
        if ($arr['avatar'] != '') { ?>
            {!! Form::label('avatar', $arr['name'] . ' Avatar:') !!}
            <?php echo "<img src='" . $arr['avatar'] . "' width='48'>"; ?>
            Primary: {!! Form::radio("primary_avatar", $siteKey, ($arr['primaryAvatar']?true:false)) !!}            
            {!! Form::hidden("site[$siteKey][avatar_src]", $arr['avatar']) !!}
            <?php
        }
        
        ?>          
        
        <hr>
        
    <?php
    
    }
    
    ?> 
    Avatars off: {!! Form::radio("primary_avatar", 'none') !!}
</ul> 


<div class="form-group">
    {!! Form::submit('Submit') !!}
</div>

{!! Form::hidden('member_id', $memberEnt->id) !!}


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