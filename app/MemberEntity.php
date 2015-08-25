<?php

/**
 * Properties and methods for single member. Properties are not 'members' table columns alone.
 * 
 * PHP version 5.6
 * 
 * @author matt  
 */
namespace App;

/**
 * Not represented : member may have multiple parent_ids, child_ids, sources and member_social_ids, but only one 
 * of each is settable in this class
 * TODO add raw_description and formatted_description
 */
class MemberEntity extends ModelNA
{

    protected $table = 'members';

    protected $fillable = array(
            'id', 'created_at', 'updated_at', 'name', 'avatar', 'child_id', 'parent_id'
        );

    //member table columns
    public $id;
    public $created_at;
    public $updated_at;
    public $name = '';
    public $display_name = '';
    public $website = '';

    // non table columns
    public $avatar = '';
    public $child_id = 0;
    public $parent_id = 0;
    public $description = '';
    public $source;
    public $member_social_id;

    /** 
     * TODO look into init instance via constructor. Since this entity extends Model base class, look into
     * the implications of that.
     * 
     * @param array $arr attributes
     */
    public function __construct(array $arr = array()) 
    {
        parent::__construct($arr);
    }

    /**
     * Pass in an array with keys that correspond to this class properties to set the values
     * of the array to those properties and return and instance of the this class
     *
     * @param array $arr array of class properties as keys
     *
     * @return object instance of this class
     */
    public function init(array $arr)
    {
        $this->member_id = !empty($arr['member_id']) ? $arr['member_id'] : null;
        $this->id = isset($arr['id']) ? $arr['id'] : $this->member_id;

        $this->created_at = isset($arr['created_at']) ? $arr['created_at'] : null;
        $this->updated_at = isset($arr['updated_at']) ? $arr['updated_at'] : null;
        $this->website = !empty($arr['website']) ? $arr['website'] : '';

        $this->name = isset($arr['name']) ? $arr['name'] : '';
        $this->display_name = !empty($arr['display_name']) ? $arr['display_name'] : $this->name;

        // properties that do not exist as columns in member table
        $this->avatar = isset($arr['avatar']) ? $arr['avatar'] : 0;
        $this->child_id = isset($arr['child_id']) ? $arr['child_id'] : 0;
        $this->parent_id = isset($arr['parent_id']) ? $arr['parent_id'] : null;
        $this->description = isset($arr['description']) ? $arr['description'] : '';
        // tbd
        $this->source = isset($arr['source']) ? $arr['source'] : null;
        $this->member_social_id = isset($arr['member_social_id']) ? $arr['member_social_id'] : null;

        return $this;

    }

    /**
     * Insert new row into members table using already set instance properties
     * 
     * @return \App\MemberEntity
     */
    public function insertMember() 
    {

        if (empty($this->name)) {
            return false;
        }

        $this->id = \DB::table('members')->insertGetId(
            [
            'name' => $this->name,
            'display_name' => $this->display_name,
            'website' => $this->website
            ]
        );

        return $this;

    }

    /**
     * Update member using instance properties
     * 
     * @return int number affected rows
     */
    public function updateMember()
    {

        if (empty($this->id)) {
            throw new \Exception('Trying to update a member that has no primary key id');
        }

        return \DB::table('members')->where('id', $this->id)
            ->update(
                ['name' => $this->name, 'display_name' => $this->display_name, 'website' => $this->website]
            );
    }

    /**
     * Get a member with numeric id, return empty entity if not found 
     * 
     * @param int $id primary key
     * 
     * @return \App\MemberEntity
     */
    public function getMemberDB($id)
    {
        $r = \DB::table('members')->whereId($id)->get();
        if (!isset($r[0])) {
            return $this;
        }

        return $this->init(get_object_vars($r[0]));

    }

    /**
     * Set description
     * 
     * @param string $desc user generated description text
     * 
     * @return \App\MemberEntity
     */
    public function setDescription($desc)
    {
        $this->description = $desc;
        return $this;
    }

    /**
     * Get description cleaned
     * 
     * @return strung
     */
    public function getDescription()
    {
        return $this->cleanText($this->description);
    }

    /**
     * Set member social id 
     * 
     * @param type $member_social_id (twitter screenname, intstagram username, etc)
     * 
     * @return \App\MemberEntity
     */
    public function setMemberSocialId($member_social_id)
    {
        $this->member_social_id = $member_social_id;
        return $this;
    }

    /**
     * get member social id
     * 
     * @return string
     */
    public function getMemberSocialId()
    {
        return $this->member_social_id;
    }

    /**
     * Set source of member data 
     * 
     * @param string $source s(eg. twitter, instagram, etc.)
     * 
     * @return \App\MemberEntity
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     * 
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set category parent of member
     * 
     * @param int $parent_id parent category
     * 
     * @return \App\MemberEntity
     */
    public function setParentId($parent_id) 
    {
        $this->parent_id = $parent_id;
        return $this;
    }

    /**
     * Set category child of member
     * 
     * @param int $child_id child category
     * 
     * @return \App\MemberEntity
     */
    public function setChildId($child_id) 
    {
        $this->child_id = $child_id;
        return $this;
    }

    /**
     * Set member id
     * 
     * @param int $id primary key from members table
     * 
     * @return \App\MemberEntity
     */
    public function setId($id) 
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get member id
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value for member table
     * 
     * @param string $created_at date of creation
     * 
     * @return \App\MemberEntity
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * Set value for member table column
     * 
     * @param string $updated_at date of update
     * 
     * @return \App\MemberEntity
     */
    public function setUpdatedAt($updated_at) 
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * Set member name
     * 
     * @param string $name member name
     * 
     * @return \App\MemberEntity
     */
    public function setName($name) 
    {
        $this->name = $this->cleanText($name);
        $this->name = $this->correctNameCase($name);
        return $this;
    }

    /**
     * Get member name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set avatar (url) of member
     * 
     * @param string $avatar url to image
     * 
     * @return \App\MemberEntity
     */
    public function setAvatar($avatar) 
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Get member avatar
     * 
     * @return type
     */
    public function getAvatar() 
    {
        return $this->avatar;
    }

}