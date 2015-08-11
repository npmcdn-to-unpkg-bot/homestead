@extends('app')

@section('content')

<?php
  
if (isset($errorArr['error'])) {
    
    echo $errorArr['error'];

} else if (count($noMemberIdArr) > 0) {

    echo "<table border='0' cellpadding='4' cellspacing='0'>";
    foreach($noMemberIdArr as $obj) {

        $username = $obj->getMemberSocialId();
        $name = $obj->getName();
        
        echo "<tr>";
        echo "<td>";
        echo "<a target='_blank' href='/members/search?search=" . $username . "'>$username</a> &nbsp; "; 
        echo "</td>";
        echo "<td>";
        echo "<td>";
        if (strstr($name, " ")) {
            $arr = explode(" ", $name);
            foreach($arr as $val) {
                echo "<a target='_blank' href='/members/search?search=" . rawurlencode($val) . "'>$val</a> &nbsp; ";
            }
        } else {
            echo "<a target='_blank' href='/members/search?search=" . rawurlencode($name) . "'>$name</a> &nbsp; "; 
        }
        echo "</td>";
        echo "<td>";
        if ($obj->getSource() == 'instagram') {
            echo "On instagram: <a target='_blank' href='http://instagram.com/" . $username . "'>$username</a>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo '<p>Nothing found</p>';
}

?>

@endsection


