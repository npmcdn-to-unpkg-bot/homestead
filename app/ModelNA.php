<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of ModelNA
 *
 * @author matt
 */
class ModelNA extends Model{
    
    public function getQuery($r)
    {

        $execute = $r->get();
        $q = $r->toSql();
        $arr = $r->getBindings();
        $pdo = \DB::connection()->getPdo();

        foreach($arr as $val) {
           $q = preg_replace('~= \?~', '= ' . $pdo->quote($val), $q);
        }

        return $q;

    }
    
    public function callCurl($url) 
    {
        
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
        
        return $output;
        
        
    }
    
}
