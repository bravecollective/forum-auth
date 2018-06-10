<?php
namespace Brave\ForumAuth\Model;

/**
 *
 * @Entity(repositoryClass="Brave\ForumAuth\CharacterRepository")
 * @Table(name="characters")
 */
class Character
{
    /**
     * EVE character ID.
     *
     * @Id
     * @Column(type="bigint")
     * @NONE
     * @var integer
     */
    private $id;

    /**
     * EVE character name.
     *
     * @Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * Forum user name.
     *
     * @Column(type="string", length=255)
     * @var string
     */
    private $username;

    /**
     * Forum password.
     *
     * @Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $password;

    /**
     *
     * @OneToMany(targetEntity="Group", mappedBy="character")
     * @var \Doctrine\Common\Collections\Collection
     */
    private $groups;

    /**
     * Last Core update.
     *
     * @Column(type="datetime", name="last_update", nullable=true)
     * @var \DateTime
     */
    private $lastUpdate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Character
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Character
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Character
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Character
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Add group
     *
     * @param Group $group
     *
     * @return Character
     */
    public function addGroup(Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param Group $group
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return Group[]
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     *
     * @return string[]
     */
    public function getGroupNames()
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     *
     * @param string $groupName
     * @return Group|null
     */
    public function getGroupByName($groupName)
    {
        foreach ($this->getGroups() as $group) {
            if ($group->getName() === $groupName) {
                return $group;
            }
        }
    }

    public function generatePassword($length = 10)
    {
        $alphabet = "abcdefghkmnpqrstuvwxyzABCDEFGHKMNPQRSTUVWXYZ23456789";
        $pass = "";
        for ($i = 0; $i < $length; $i ++) {
            $pass = $pass . substr($alphabet, hexdec(bin2hex(random_bytes(1))) % strlen($alphabet), 1);
        }

        return $pass;
    }

    /**
     * Set lastUpdate.
     *
     * @param \DateTime $update
     *
     * @return Character
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = clone $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate.
     *
     * @return \DateTime|null
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * TODO implement this.
     *
     * @return string
     */
    public function getCorporationName()
    {
        return '';
    }

    /**
     * TODO implement this.
     *
     * @return string|null
     */
    public function getAllianceName()
    {
        return null;
    }
}
