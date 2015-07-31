<?php
namespace App;

/**
 * Description of MemberEntity
 *
 * @author matt
 */
class MemberEntity extends ModelNA {
    
    protected $table = 'members';
    
    protected $fillable = array('id', 'created_at', 'updated_at', 'name', 'avatar', 'child_id', 'parent_id');
    
    public $id;
    public $created_at;
    public $updated_at;
    public $name = '';
    public $avatar = '';
    public $child_id = 0;
    public $parent_id = 0;
    public $memberSocialIdArr = array();
    public $description = '';
    
    public function setDescription($desc)
    {
        $this->description = $desc;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->cleanText($this->description);
    }
    
    public function setMemberSocialIdArr($memberSocialId, $socialSiteId)
    {
        $this->memberSocialIdArr[$socialSiteId] = $memberSocialId;
        return $this;
    }
    
    public function getMemberSocialIdArr()
    {
        return $this->memberSocialIdArr;
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

    public function setAvatar($avatar) {
        $this->avatar = $avatar;
        return $this;
    }


    
}
