<?php

namespace App\Model\Work\Entity\Projects\Role;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_role')]
class  Role
{
    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_role_id')]
    private $id;

    #[ORM\Column(type: 'string', unique: true)]
    private $name;

    /**
     * @var ArrayCollection|Permission[]
     */
    #[ORM\Column(type: 'work_projects_role_permissions')]
    private $permissions;

    public function __construct(Id $id, string $name, array $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->setPermissions($permissions);
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPermissions(): array
    {
        return $this->permissions->toArray();
    }

    public function setPermissions(ArrayCollection|array $permissions): void
    {
        $this->permissions = new ArrayCollection(
            array_map(static function (string $name) {
                return new Permission($name);
            }, array_unique($permissions))
        );
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions->exists(static function (Permission $current) use ($permission) {
            return $current->isNameEqual($permission);
        });
    }

    public function clone(Id $id, string $name): Role
    {
        return new self($id, $name, $this->permissions->toArray());
    }

    public function edit(string $name, array $values)
    {
        $this->name = $name;
        $this->setPermissions($values);
    }
}