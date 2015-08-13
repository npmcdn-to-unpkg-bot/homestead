@extends('app')

@section('content')

<style>
    td{
        padding:4px;
    }
</style>

<?php
  
if (isset($errorArr['error'])) {
    
    echo $errorArr['error'];

} else if (count($noMemberIdArr) > 0) {

    echo "<b>Search Members</b>";
    echo "<table>";
    echo "<tr>";
    echo "<td>Whole Name</td>";
    echo "<td>Words in Name</td>";
    echo "<td>View on social site</td>";
    echo "</tr>";
    foreach($noMemberIdArr as $obj) {

        $username = $obj->getMemberSocialId();
        $name = $obj->getName();
        
        echo "<tr>";
        echo "<td>";
        echo "<a target='_blank' href='/members/search?search=" . $username . "'>$username</a> &nbsp; "; 
        echo "</td>";
        
        echo "<td>";

        // break camel case letters strung together into their own word
        // will capture all lowercase word into single word
        preg_match_all('/((?:^|[A-Z])[a-z]+)/',$name, $matches);

        // make individual words (separated by spaces) clickable
        if (strstr($name, " ")) {
            $arr = explode(" ", $name);
            foreach($arr as $val) {
                echo "<a target='_blank' href='/members/search?search=" . rawurlencode($val) . "'>$val</a> &nbsp; ";
            }
         echo "<br>";           
        }

        foreach($matches[0] as $val) {
            echo "<a target='_blank' href='/members/search?search=" . rawurlencode(trim($val)) . "'>";
            echo trim($val) . "</a> &nbsp; "; 
        }
        echo "</td>";
        echo "<td>";
        if ($obj->getSource() == 'instagram') {
            echo "<a target='_blank' href='http://instagram.com/" . $username . "'>$username</a>";
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


