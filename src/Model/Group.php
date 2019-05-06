<?php
namespace Brave\ForumAuth\Model;

/**
 *
 * @Entity
 * @Table(name="character_groups")
 */
class Group
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * Group name.
     *
     * @Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @ManyToOne(targetEntity="Character", inversedBy="groups")
     * @var Character
     */
    private $character;

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
     * @return Group
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
     * Set character
     *
     * @param Character $character
     *
     * @return Group
     */
    public function setCharacter(Character $character = null)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return Character
     */
    public function getCharacter()
    {
        return $this->character;
    }
}
