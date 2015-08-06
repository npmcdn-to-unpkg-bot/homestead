@extends('app')

@section('content')

<?php

$numCols = 3;

renderCategoryPath($catPathArr);

echo "<br>";

foreach($memberArr as $i => $obj) { ?>
    
@include('socialmedia/partials/content', ['obj' => $obj])
   
<?php }

//printR($catPathArr);
//printR($memberArr);
//printR($contentArr);

?>

<script>
<?php echo 'contentArr=' . json_encode($contentArr); ?>
</script>    

<script src='/js/nowarena.js'></script>


@endsection