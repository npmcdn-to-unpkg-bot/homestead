@extends('app')

@section('content')

<?php

renderCategoryPath($catPathArr);
?>

<script>
    
displayArr=[];
memberIdArr=[];
memberNameArr=[];

<?php

foreach($parentArr['memberArr'] as $childId => $memberArr) {
    
    echo "displayArr[$childId]=[];\n";
    echo "memberIdArr[$childId]=[];\n";
    echo "memberNameArr[$childId]=[]\n";
    
    foreach($memberArr as $i => $obj) {
    
        echo "displayArr[$childId][$i]='" . (($i ==0 ) ? "block" : "none") . "';\n";
        echo "memberIdArr[$childId][$i] = " . $obj->id . ";\n";
        echo "memberNameArr[$childId][$i] = \"" . $obj->name . "\";\n";
        
    }
    
}
echo "\n</script>";
echo "<br>";


foreach($parentArr['memberArr'] as $childId => $memberArr) {

    echo "<div class='parentCont'>";
    
    echo "<div class='parentTitleCont' style='float:left;'>";
        echo "<span class='parentTitle'>";
        echo "<a href='/socialmedia/" . str_slug($childrenArr[$childId], "_") . "'>";
        echo $childrenArr[$childId];
        echo "</a></span>";
    echo "</div>";
    
    echo "<div class='childrenNavTop'>";
        $childrenNav = "<a href='javascript:void(0);' class='childPrev' id='child_$childId'>&laquo;Prev</a>";   
        $childrenNav.= " - ";
        $childrenNav.= "<a href='javascript:void(0);' class='childNext' id='child_$childId'>Next ";
        $childrenNav.= "<span class='next_member_" . $childId . "'>&nbsp;</span>";
        $childrenNav.= "&raquo;</a>";
        echo $childrenNav;
    echo "</div>";

    echo "<div style='clear:both;'></div>";
    
    echo "<div class='parentHolder'>";    

    $count = count($memberArr);
    foreach($memberArr as $i => $obj) { 

        $class = ($i == 0) ? 'childrenHolderBlock': 'childrenHolderNone'; 
        echo "<div class='$class' id='stack_" . $obj->id ."' style='z-index:" . $count . ";'>"; 
        $count--;
        
        ?>

        @include('socialmedia/partials/content', ['obj' => $obj])

        </div>

    <?php 
    
    }

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
<script src='/js/nowarena.js'></script>

@endsection