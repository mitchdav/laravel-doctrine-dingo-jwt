<?php

namespace App\API\V1\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TempestTools\Moat\Contracts\PermissionContract;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\PermissionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Permission extends EntityAbstract implements PermissionContract
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\User", inversedBy="permissions", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *     name="UserToPermission",
     *     joinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Role", inversedBy="permissions", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *     name="RoleToPermission",
     *     joinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     */
    private $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->roles = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId():?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Permission
     */
    public function setName(string $name): Permission
    {
        $this->name = $name;
        return $this;
    }


    /**
     * @return Collection|NULL
     */
    public function getUsers(): ?Collection
    {
        return $this->users;
    }


    /**
     * @return Collection|NULL
     */
    public function getRoles(): ?Collection
    {
        return $this->roles;
    }

    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Permission
     */
    public function addUser(User $user, bool $preventLoop = false): Permission
    {
        if ($preventLoop === false) {
            $user->addPermission($this, true);
        }

        $this->users[] = $user;
        return $this;
    }

    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Permission
     */
    public function removeUser(User $user, bool $preventLoop = false): Permission
    {
        if ($preventLoop === false) {
            $user->removePermission($this, true);
        }
        $this->users->removeElement($user);
        return $this;
    }

    /**
     * @param Role $role
     * @param bool $preventLoop
     * @return Permission
     */
    public function addRole(Role $role, bool $preventLoop = false): Permission
    {
        if ($preventLoop === false) {
            $role->addPermission($this, true);
        }

        $this->roles[] = $role;
        return $this;
    }

    /**
     * @param Role $role
     * @param bool $preventLoop
     * @return Permission
     */
    public function removeRole(Role $role, bool $preventLoop = false): Permission
    {
        if ($preventLoop === false) {
            $role->removePermission($this, true);
        }
        $this->roles->removeElement($role);
        return $this;
    }

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'create'=>[
                    'allowed'=>false,
                    'settings'=>[
                        'validate'=>[ // the only thing we enforce on artists is the validator
                            'rules'=>[
                                'name'=>'required|min:2',
                            ],
                            'messages'=>NULL,
                            'customAttributes'=>NULL,
                        ],
                    ],
                    'toArray'=> [
                        'id'=>[],
                        'name'=>[],
                        'users'=>[],
                        'roles'=>[],
                    ]
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
            ],
            'superAdmin'=>[ // can do everything in default, and is allowed to do it when a super admin
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
            ],
            // Below here is for testing purposes only
            'testing'=>[
                'create'=>[
                    'allowed'=>true,
                    'extends'=>[':default:create'],
                ],
                'update'=>[
                    'allowed'=>true,
                    'extends'=>[':default:create'],
                ],
                'delete'=>[
                    'allowed'=>true,
                    'extends'=>[':default:create'],
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
            ],
        ];
    }
}
