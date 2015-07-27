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
    
}
