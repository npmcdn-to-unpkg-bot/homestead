<?php
namespace App;

/**
 * 
 * Not represented : member may have multiple parent_ids, child_ids, sources and member_social_ids
 *
 * @author matt
 */
class MemberEntity extends ModelNA {
    
    protected $table = 'members';
    
    protected $fillable = array('id', 'created_at', 'updated_at', 'name', 'avatar', 'child_id', 'parent_id');
    
    //member table columns
    public $id;
    public $created_at;
    public $updated_at;
    public $name = '';
    public $display_name = '';
    
    // non table columns
    public $avatar = '';
    public $child_id = 0;
    public $parent_id = 0;
    public $description = '';
    public $source;
    public $member_social_id;

    public function __construct(array $arr = array()) {
        parent::__construct($arr);

         
    }
    
    public function init($arr)
    {
        $this->member_id = !empty($arr['member_id']) ? $arr['member_id'] : NULL;
        $this->id = isset($arr['id']) ? $arr['id'] : $this->member_id;
        
        $this->created_at = isset($arr['created_at']) ? $arr['created_at'] : NULL;        
        $this->updated_at = isset($arr['updated_at']) ? $arr['updated_at'] : NULL;
        
        $this->name = isset($arr['name']) ? $arr['name'] : '';
        $this->display_name = !empty($arr['display_name']) ? $arr['display_name'] : $this->name;
        
        
        $this->avatar = isset($arr['avatar']) ? $arr['avatar'] : 0;
        $this->child_id = isset($arr['child_id']) ? $arr['child_id'] : 0;  
        $this->parent_id = isset($arr['parent_id']) ? $arr['parent_id'] : NULL;
        $this->description = isset($arr['description']) ? $arr['description'] : '';  
        // tbd
        $this->source = isset($arr['source']) ? $arr['source'] : NULL; 
        $this->member_social_id = isset($arr['member_social_id']) ? $arr['member_social_id'] : NULL;
        
        return $this;
        
    }
    
    public function insertMember() {
        
        if (empty($this->name)) {
            return false;
        }
        
        $this->id = \DB::table('members')->insertGetId([
            'name' => $this->name,
            'display_name' => $this->display_name
        ]);
        
        return $this;
            
    }
    
    public function updateMember() 
    {
        // TODO throw exception
        if (empty($this->id)) {
            return false;
        }

        return \DB::table('members')->where('id', $this->id)
            ->update(
                ['name' => $this->name, 'display_name' => $this->display_name]
            );
    }
    
    public function getMemberDB($id)
    {
        $r = \DB::table('members')->whereId($id)->get();
        if (!isset($r[0])) {
            return $this;
        }

        return $this->init(get_object_vars($r[0]));

    }
    
    public function setDescription($desc)
    {
        $this->description = $desc;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->cleanText($this->description);
    }
    
    public function setMemberSocialId($memberSocialId)
    {
        $this->member_social_id = $memberSocialId;
        return $this;
    }
    
    public function getMemberSocialId()
    {
        return $this->member_social_id;
    }
    
    public function setSource($source) 
    {
        $this->source = $source;
        return $this;
    }
    
    public function getSource()
    {
        return $this->source;
    }
    
    public function setParentId($parent_id) {
        $this->parent_id = $parent_id;
        return $this;
    }
    
    public function setChildId($child_id) {
        $this->child_id = $child_id;
        return $this;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setCreated_at($created_at) {
        $this->created_at = $created_at;
        return $this;
    }

    public function setUpdated_at($updated_at) {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function setName($name) {
        $this->name = $this->cleanText($name);
        $this->name = $this->correctNameCase($name);
        return $this;
    }
    
    public function getName(){
        return $this->name;
    }

    public function setAvatar($avatar) {
        $this->avatar = $avatar;
        return $this;
    }

    public function getAvatar() {
        return $this->avatar;
    }

    
}
