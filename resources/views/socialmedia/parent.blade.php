@extends('app')

@section('content')

<?php

renderCategoryPath($catPathArr);

if (!empty($parentArr['memberArr'])) {
    
?>

<script>
    
displayArr=[];
memIdArr=[];
memNameArr=[];
memAvatarArr= [];
memIdChildIdArr = [];

<?php

foreach($parentArr['memberArr'] as $childId => $memberArr) {
    
    echo "memIdArr[$childId]=[];\n";
    echo "memNameArr[$childId]=[];\n";
    echo "memAvatarArr[$childId]=[];\n";    

    
    foreach($memberArr as $i => $obj) {
    
        echo "memIdArr[$childId][$i] = " . $obj->id . ";\n";
        echo "memNameArr[$childId][$i] = \"" . $obj->name . "\";\n";
        echo "memAvatarArr[$childId][$i] = \"" . $obj->avatar . "\";\n";
        echo "memIdChildIdArr[" . $obj->id . "] = $childId;\n";
        
    }
    
}


echo "\n</script>";
echo "<br>";

$zIndexParent = 0;

foreach($parentArr['memberArr'] as $childId => $memberArr) {

    echo "<div class='parentCont'>";
    
    echo "<div class='parentTitleCont' style='float:left;'>";
        echo "<span class='parentTitle'>";
        echo "<a href='/socialmedia/" . str_slug($catArr[$childId], "_") . "'>";
        echo $catArr[$childId];
        echo "&raquo;</a></span>";

    echo "</div>";
    
    echo "<div class='childrenNavTop'>";
        $childrenNav = "<a data-childid='$childId' href='javascript:void(0);' class='childPrev'>&laquo;Prev</a>";   
        $childrenNav.= " - ";
        $childrenNav.= "<a data-childid='$childId' href='javascript:void(0);' class='childNext'>Next ";
        $childrenNav.= "<span class='next_child_id_" . $childId . "'>&nbsp;</span>";
        $childrenNav.= "&raquo;</a>";
        echo $childrenNav;
    echo "</div>";

    echo "<div style='clear:both;'></div>";
    
    echo "<div class='parentHolder'>";    

    $count = $zIndexParent + count($memberArr);
    foreach($memberArr as $i => $obj) { 

        $class = ($i == 0) ? 'childrenHolderBlock': 'childrenHolderNone'; 
        echo "<div class='$class' id='stack_" . $childId ."'>";// style='z-index:" . $count . ";'>"; 
        $count--;
        
        ?>

        @include('socialmedia/partials/contentparent', ['obj' => $obj, 'childId' => $childId])

        </div>

    <?php 
    
        break;
    
    }
    
    //$zIndexParent-=500;

    echo '</div>';
 
    echo "<div class='childrenNavBottom'>";
    echo $childrenNav;
    echo "</div>";
            
    echo '</div>';
        
    echo '<br><div style="clear:both;"></div>';
}
//printR($memberArr);
?>

<script>
<?php echo 'contentArr=' . json_encode($parentArr['contentArr']); ?>
</script>    
<script src='/js/nowarena_parent.js'></script>

<?php } else {
    echo 'no content yet';
}
?>

@endsection