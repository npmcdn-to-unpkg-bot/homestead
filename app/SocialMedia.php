<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
/**
 * 
 *
 * @author matt
 */
class SocialMedia extends Model{
    
    protected $fillable = array('id', 'member_id', 'social_id', 'member_social_id','text', 'created_at', 'media_url', 'media_height', 'media_width', 'link', 'source');

    public $id;
    public $member_id;
    public $social_id;
    public $member_social_id;
    /* @var string $text */
    public $text;        
    public $media_url;
    public $media_height;
    public $media_width;
    public $link;
    public $source;
    

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setMemberId($member_id) {
        $this->member_id = $member_id;
        return $this;
    }

    function setSocialId($social_id) {
        $this->social_id = $social_id;
        return $this;
    }

    function setMemberSocialId($member_social_id) {
        $this->member_social_id = $member_social_id;
        return $this;
    }

    function setText($text) {

        $text = preg_replace("~\\n|\\r~", " ", $text);
        $text = trim($text); 
        // remove non-ascii chars
        //$text = preg_replace('/[^(\x20-\x7F)]*/','', $text);
        $this->text = $this->convertQuotes($text);
        //$this->text = iconv("UTF-8", "UTF-8//IGNORE", $text);
        //$this->text = mb_convert_encoding(trim($text), 'UTF-8', 'UTF-8');;
        return $this;
    }

    function setMediaUrl($media_url) {
        $this->media_url = $media_url;
        return $this;
    }

    function setMediaHeight($media_height) {
        $this->media_height = $media_height;
        return $this;
    }

    function setMediaWidth($media_width) {
        $this->media_width = $media_width;
        return $this;
    }

    function setLink($link) {
        $this->link = $link;
        return $this;
    }

    function setSource($source) {
        $this->source = $source;
        return $this;
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
    
}
