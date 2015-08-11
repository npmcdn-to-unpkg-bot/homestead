@extends('app')

@section('content')

<?php

if (isset($notOnSiteArr) && count($notOnSiteArr) >0 ) {
    
    if (isset($notOnSiteArr['error'])) {
        echo $notOnSiteArr['error'];
        return;
    }
    
    echo "<table border='0' cellpadding='4' cellspacing='0'>";
    foreach($notOnSiteArr as $username => $name) {
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
        if ($socialSite == 'instagram') {
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


