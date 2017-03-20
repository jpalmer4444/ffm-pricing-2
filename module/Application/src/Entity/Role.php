<?php

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @Table(name="roles")
 */
class Role
{
    const ROLE_ADMIN = 'admin';

    /**
     * @Id
     * @Column(type="integer",
     * columnDefinition="INT(11)")
     * @GeneratedValue(strategy="AUTO")
     *
     * @var int|null
     */
    protected $id;

    /**
     * @var string|null
     *
     * @Column(type="string", length=48, unique=true)
     */
    protected $name;

    /**
     * @var Collection
     *
     * @ManyToMany(targetEntity="Permission", indexBy="name", cascade={"persist"}, inversedBy="roles")
     */
    protected $permissions;

    /**
     * @var Collection|User[]
     *
     * @ManyToMany(targetEntity="User", mappedBy="roles")
     */
    protected $users;

    /**
     * Init the Doctrine collection
     */
    public function __construct()
    {
        $this->permissions  = new ArrayCollection();
        $this->users        = new ArrayCollection();
    }

    /**
     * Get the role identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the role name
     *
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Get the role name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param ArrayCollection $permissions
     * @return $this
     */
    public function addPermissions(ArrayCollection $permissions)
    {
        foreach ($permissions as $permission) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasPermission($permission)
    {
        /** @var Permission $permission_obj */
        foreach ($this->getPermissions() as $permission_obj) {
            if ($permission == $permission_obj->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ArrayCollection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    public function clearPermissions()
    {
        $this->permissions->clear();
    }

    /**
     * @param ArrayCollection $permissions
     * @return $this
     */
    public function removePermissions(ArrayCollection $permissions)
    {
        foreach ($permissions as $permission) {
            $this->permissions->removeElement($permission);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'permissions'   => $this->permissions->toArray()
        ];
    }

    /**
     * Get Users
     *
     * @return array
     */
    public function getUsers()
    {
        return $this->users->toArray();
    }
}
