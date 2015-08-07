<?php
use \App\Site;
?>

@extends('app')

@section('content')


<?php 
echo "<div style='max-width:700px;margin:0px auto;'>";
?>
<h2 class='site_subject'><?php echo Site::getInstance()->getNameShort();?> Social Media</h2>
<?php
renderTree($parentChildArr, $categoriesArr);
echo "</div>";
?> 

@endsection