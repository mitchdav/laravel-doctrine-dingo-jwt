<?php

namespace App\API\V1\Entities;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping AS ORM;
use TempestTools\Moat\Contracts\RoleContract;
use Doctrine\Common\Collections\ArrayCollection;
use TempestTools\Moat\Entity\HasPermissionsOptimizedTrait;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\RoleRepository")
 * @ORM\Table(name="roles")
 * @ORM\HasLifecycleCallbacks
 */
class Role extends EntityAbstract implements RoleContract
{
    use HasPermissionsOptimizedTrait;
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
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\User", inversedBy="roles", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *     name="UserToRole",
     *     joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Permission", mappedBy="roles", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    private $permissions;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
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
    public function getName():string
    {
        return $this->name;
    }

    /**
     * @return Collection|NULL
     */
    public function getPermissions(): ?Collection
    {
        return $this->permissions;
    }

    /**
     * @param string $name
     * @return Role
     */
    public function setName(string $name):Role
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
     * @param User $user
     * @param bool $preventLoop
     * @return Role
     */
    public function addUser(User $user, bool $preventLoop = false): Role
    {
        if ($preventLoop === false) {
            $user->addRole($this, true);
        }

        $this->users[] = $user;
        return $this;
    }

    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Role
     */
    public function removeUser(User $user, bool $preventLoop = false): Role
    {
        if ($preventLoop === false) {
            $user->removeRole($this, true);
        }
        $this->users->removeElement($user);
        return $this;
    }

    /**
     * @param Permission $permission
     * @param bool $preventLoop
     * @return Role
     */
    public function addPermission(Permission $permission, bool $preventLoop = false): Role
    {
        if ($preventLoop === false) {
            $permission->addRole($this, true);
        }

        $this->permissions[] = $permission;
        return $this;
    }

    /**
     * @param Permission $permission
     * @param bool $preventLoop
     * @return Role
     * @internal param User $user
     */
    public function removePermission(Permission $permission, bool $preventLoop = false): Role
    {
        if ($preventLoop === false) {
            $permission->removeRole($this, true);
        }
        $this->permissions->removeElement($permission);
        return $this;
    }

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'create'=>[ // the only thing we enforce on artists is the validator
                    'allowed'=>false,
                    'settings'=>[
                        'validate'=>[
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
                        'permissions'=>[],
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