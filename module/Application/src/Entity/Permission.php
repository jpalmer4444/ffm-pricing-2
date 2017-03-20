<?php

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @Table(name="permissions")
 */
class Permission
{
    /**
     * @var int|null
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null
     *
     * @Column(type="string", length=128, unique=true)
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\Collection|Role[]
     *
     * @ManyToMany(targetEntity="Role", mappedBy="permissions")
     */
    protected $roles;

    /**
     * @var string|null
     *
     * @Column(type="string", length=128)
     */
    protected $title;

    /**
     * Constructor
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->name  = (string) $name;
        $this->roles = new ArrayCollection();
    }

    /**
     * Get the permission identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->name;
    }

    public function getName()
    {
        return $this->__toString();
    }

    /**
     * @param null|string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'title' => $this->title,
        ];
    }

    /**
     * Get Roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }
}
