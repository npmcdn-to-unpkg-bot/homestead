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
            //echo $pdo->quote($val)."|<br>"; preg_match('~= \?~', '= ' . $pdo->quote($val), $arr);printR($arr);
            $q = preg_replace('~= \?~', '= ' . $pdo->quote($val), $q, 1);
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
    
    public function cleanText($text)
    {
        
        $text = trim($text);
        $text = preg_replace("~\\n|\\r~", " ", $text);
        $text = $this->convertMS($text);
        $text = $this->convertQuotes($text);
        $pattern = '~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $text = preg_replace($pattern, '$1', htmlentities($text, ENT_COMPAT, 'UTF-8'));
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = iconv("UTF-8", "UTF-8//IGNORE", $text);
        
        return $text;
        
    }
        
    public function convertMS($text)
    {
    	$search = array(chr(145), chr(146), chr(147), chr(148), chr(151), chr(133));
    
    	$replace = array("'", "'", '"', '"', '-', '...');
    
    	return str_replace($search, $replace, $text);
    
    }
    
    public function convertQuotes($text)
    {
        
    	$chr_map = array(
            // Windows codepage 1252
            "\xC2\x82" => "'", // U+0082->U+201A single low-9 quotation mark
            "\xC2\x84" => '"', // U+0084->U+201E double low-9 quotation mark
            "\xC2\x8B" => "'", // U+008B->U+2039 single left-pointing angle quotation mark
            "\xC2\x91" => "'", // U+0091->U+2018 left single quotation mark
            "\xC2\x92" => "'", // U+0092->U+2019 right single quotation mark
            "\xC2\x93" => '"', // U+0093->U+201C left double quotation mark
            "\xC2\x94" => '"', // U+0094->U+201D right double quotation mark
            "\xC2\x9B" => "'", // U+009B->U+203A single right-pointing angle quotation mark

            // Regular Unicode     // U+0022 quotation mark (")
            // U+0027 apostrophe     (')
            "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
            "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
            "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
            "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
            "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
            "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
            "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
            "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
            "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
            "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
            "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
            "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
    	);
    	$chr = array_keys  ($chr_map); // but: for efficiency you should
    	$rpl = array_values($chr_map); // pre-calculate these two arrays
    	$text = str_replace($chr, $rpl, html_entity_decode($text, ENT_QUOTES, "UTF-8"));
        
    	return $text;
        
    } 
    
    public function correctNameCase($text)
    {
        
        if (strtoupper($text) == $text) {
            $text = ucwords(strtolower($text));
        }
        
        return $text;
        
    }
    
}
