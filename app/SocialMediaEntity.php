<?php
namespace App;
/**
 * A single instance of social media content for a specific member and social media id
 * 
 *
 * @author matt
 */
class SocialMediaEntity extends ModelNA{
    
    protected $table = 'social_media';
    
    public $fillable = array('id', 'member_id', 'social_id', 'member_social_id','text', 'written_at', 'media_url', 'media_height', 'media_width', 'link', 'source');
    
    public $guarded = null;

    public $id;
    public $member_id;
    public $social_id;//2523590239530
    public $member_social_id;//twitterluver
    /* @var string $text */
    public $text;        
    public $media_url;
    public $media_height;
    public $media_width;
    public $link;
    public $source;//twitter
    public $written_at;
    
    public function __construct(array $attributes = array()){
        parent::__construct($attributes);
    }
    
    public function setWrittenAt($written_at)
    {
        $this->written_at = $written_at;
        return $this;
    }

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

        $this->text = $this->cleanText($text);
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

    
}
