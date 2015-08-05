<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of ModelNA
 *
 * @author matt
 */
class ModelNA extends Model{
    
    
    /*
     * Use this method along with usort() to sort an array with a 'date' value 
     * eg. usort($arr, array($this, 'sortByTime'));
     *       
     */
    public function sortByWrittenAt($a, $b)
    {
       return strtotime($a['written_at']) - strtotime($b['written_at']);  
    }
    
    
    public function setDatetime($date) 
    {
        
        if (strstr($date, "-") || strstr($date, "/") || strstr($date, '\\') || strlen($date) == 8)  {
            return new \DateTime($date);
        } else {
            return new \DateTime(date("Y-M-d H:i:s", $date));
        }
        
    }
    
    public function pluralize( $count, $text ) 
    { 
        return $count . ( ( $count == 1 ) ? ( " $text" ) : ( " ${text}s" ) );
    }
    
    public function getAge( $datetime )
    {
        
        $datetime = $this->setDatetime($datetime);
        
        $interval = date_create('now')->diff( $datetime );
        $suffix = ( $interval->invert ? ' ago' : '' );
        if ( $v = $interval->y >= 1 ) return $this->pluralize( $interval->y, 'yr' ) . $suffix;
        if ( $v = $interval->m >= 1 ) return $this->pluralize( $interval->m, 'mnth' ) . $suffix;
        if ( $v = $interval->d >= 1 ) return $this->pluralize( $interval->d, 'dy' ) . $suffix;
        if ( $v = $interval->h >= 1 ) return $this->pluralize( $interval->h, 'hr' ) . $suffix;
        if ( $v = $interval->i >= 1 ) return $this->pluralize( $interval->i, 'min' ) . $suffix;
        return $this->pluralize( $interval->s, 'second' ) . $suffix;
    }
    
    public function getQuery($r)
    {

        if (!is_array($r)) {
            $execute = $r->get();
            $q = $r->toSql();           
        }

        $arr = $r->getBindings();
        $pdo = \DB::connection()->getPdo();

        foreach($arr as $val) {
            //echo $pdo->quote($val)."|<br>"; preg_match('~= \?~', '= ' . $pdo->quote($val), $arr);printR($arr);
            $q = preg_replace('~(<=|>=|=|<|!=) \?~', '= ' . $pdo->quote($val), $q, 1);
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
        //echo "<hr>A:<br>";
        $text = preg_replace("~\\n|\\r~", " ", $text);
        
        //echo $text;
        
        //echo "<br>A.5:<br>";
        // TODO enable utf8mb4 in mysql to store emojis
        $text = $this->removeEmoji($text);
        //echo $text;
        //echo "<Br>A.6:<br>";
        //$text = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $text);
        //echo $text;
        //echo "<br>B:<Br>";
        $text = $this->convertMS($text);
        //echo $text;
        $text = $this->convertQuotes($text);
        //echo "<BR>C:<Br>";
        //echo $text;
        
        //$pattern = '~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        //$text = preg_replace($pattern, '$1', htmlentities($text, ENT_COMPAT, 'UTF-8'));
        //echo "<br>D:<bR>";
        //$text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        //echo $text;
        //echo "<br>E:<br>";
        //$text = iconv("UTF-8", "UTF-8//IGNORE", $text);
        //echo $text;
        
        return $text;
        
    }
    
    public function removeEmoji($text) {

        $clean_text = "";

        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        return $clean_text;
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
