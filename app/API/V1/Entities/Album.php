<?php
namespace App\API\V1\Entities;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;
use TempestTools\Scribe\Doctrine\Events\GenericEventArgs;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\AlbumRepository")
 * @ORM\Table(indexes={@ORM\Index(name="name_idx", columns={"name"}),@ORM\Index(name="releaseDate_idx", columns={"release_date"})})
 * @ORM\HasLifecycleCallbacks
 */
class Album extends EntityAbstract
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="release_date")
     */
    private $releaseDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\API\V1\Entities\Artist", inversedBy="albums", cascade={"persist"})
     * @ORM\JoinColumn(name="artist_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $artist;

    /**
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\User", mappedBy="albums", fetch="EXTRA_LAZY")
     * 
     */
    private $users;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        parent::__construct();
    }


    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Album
     */
    public function addUser(User $user, bool $preventLoop = false): Album
    {
        if ($preventLoop === false) {
            $user->addAlbum($this, true);
        }

        $this->users[] = $user;
        return $this;
    }

    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Album
     */
    public function removeUser(User $user, bool $preventLoop = false): Album
    {
        if ($preventLoop === false) {
            $user->removeAlbum($this, true);
        }
        $this->users->removeElement($user);
        return $this;
    }

    /**
     * @return Artist
     */
    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    /**
     * @param Artist $artist
     * @return Album
     */
    public function setArtist(Artist $artist = null): Album
    {
        $this->artist = $artist;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getReleaseDate():DateTime
    {
        return $this->releaseDate;
    }

    /**
     * @param mixed $releaseDate
     * @return Album
     */
    public function setReleaseDate($releaseDate): Album
    {
        $releaseDate = ($releaseDate instanceof DateTime)?$releaseDate:new DateTime($releaseDate);
        $this->releaseDate = $releaseDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getName():string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Album
     */
    public function setName(string $name): Album
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getId():?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Album
     */
    public function setId(int $id): Album
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getUsers(): ?Collection
    {
        return $this->users;
    }

    public function preSetField(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $array = $this->getArrayHelper()->getArray();
        if (!isset($array['entityEvents'])) {
            $array['entityEvents'] = [];
        }
        $array['entityEvents']['preSetField'] = $e->getArgs()->getArrayCopy();
    }

    public function preProcessAssociationParams(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['entityEvents']['preProcessAssociationParams']=$e;
    }

    public function prePersist(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['entityEvents']['prePersist']=$e;
    }

    public function postPersist(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['entityEvents']['postPersist']=$e;
    }


    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getTTConfig(): array
    {
        /** @noinspection NullPointerExceptionInspection */
        return [
            'default'=>[
                'create'=>[
                    'allowed'=>false, // by default this is not allowed
                    'settings'=>[
                        'validate'=>[ // Add a validator that will be inherited by all other configs
                            'rules'=>[
                                'name'=>'required|min:2',
                                'releaseDate'=>'required|date'
                            ],
                            'messages'=>NULL,
                            'customAttributes'=>NULL,
                        ],
                    ],
                    'toArray'=> [
                        'id'=>[],
                        'name'=>[],
                        'releaseDate'=>[],
                        'artist'=>[],
                        'users'=>[],
                    ]
                ],
                'update'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
                'delete'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create'],
                ],
            ],
            'user'=>[
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>false, // users can't allowed to create
                    'permissive'=>false, // the following rules are defined here in order to be inherited further down
                    'fields'=>[
                        'users'=>[ // when this is inherited a user will be able to add them selves to an album
                            'permissive'=>false,
                            'settings'=>[
                                'enforce'=>[
                                    'id'=>$this->getArrayHelper()->parseArrayPath([CommonArrayObjectKeyConstants::USER_KEY_NAME, 'id']) // When adding them selves to an album we enforce that the user is assigning their own user entity to the album
                                ],
                            ],
                            'assign'=>[ // the can only add and remove them selves from an album
                                'add'=>true,
                                'remove'=>true,
                                'addSingle'=>true,
                                'removeSingle'=>true,
                            ],
                            'chain'=>[
                                'read'=>true // Then can only add existing users, they can not create update or delete users in the process
                            ]
                        ]
                    ],
                ],
                'update'=>[
                    'extends'=>[':user:create'], // The same as create but it's allowed this time
                    'allowed'=>true,
                ],
                'delete'=>[
                    'extends'=>[':user:create']
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
            ],
            'admin'=>[ // Extends default because default has no additional rules on it, so super admins can do anything
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
            'superAdmin'=>[ // Extends default because default has no additional rules on it, so super admins can do anything
                'create'=>[
                    'extends'=>[':admin:create'],
                ],
                'update'=>[
                    'extends'=>[':admin:create'],
                ],
                'delete'=>[
                    'extends'=>[':admin:create'],
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':admin:create']
                ],
            ],
            // Below here is for testing purposes only
            'testPermissive1'=>[
                'create'=>[
                    'permissive'=>true, // the following rules are defined here in order to be inherited further down
                    'fields'=>[
                        'name'=>[
                            'permissive'=>false,
                        ]
                    ]
                ]
            ],
            'testPermissive2'=>[
                'create'=>[
                    'permissive'=>true, // the following rules are defined here in order to be inherited further down
                    'fields'=>[
                        'name'=>[
                            'permissive'=>false,
                            'assign'=>[ // the can only add and remove them selves from an album
                                'set'=>true
                            ],
                        ]
                    ]
                ]
            ],
            'testTopLevelSetToAndMutate'=>[
                'create'=>[
                    'settings'=>[
                        'setTo'=>[
                            'name'=>'foo',
                        ],
                        'mutate'=>function ($extra) {
                            /** @var Album $self */
                            $self = $extra['self'];
                            $currentName = $self->getName();
                            $newName = $currentName . 'bar';
                            $self->setName($newName);
                        }
                    ]
                ]
            ],
            'testEnforceTopLevelWorks'=>[
                'create'=>[
                    'settings'=>[
                        'enforce'=>[
                            'name'=>'NOT BEETHOVEN'
                        ]
                    ]
                ]
            ],
            'testTopLevelClosure'=>[
                'create'=>[
                    'settings'=>[
                        'closure'=>function () {
                            return false;
                        }
                    ]
                ]
            ],
            'testLowLevelEnforce'=>[
                'create'=>[
                    'fields'=>[
                        'name'=>[
                            'settings'=>[
                                'enforce'=>'foo'
                            ]
                        ]
                    ]
                ]
            ],
            'testLowLevelEnforceOnRelation'=>[
                'create'=>[
                    'fields'=>[
                        'artist'=>[
                            'settings'=>[
                                'enforce'=>[
                                    'name'=>'Bob the artist'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'testLowLevelClosure'=>[
                'create'=>[
                    'fields'=>[
                        'name'=>[
                            'settings'=>[
                                'closure'=>function () {
                                    return false;
                                }
                            ]
                        ]
                    ]
                ]
            ],
            'testLowLevelMutate'=>[
                'create'=>[
                    'fields'=>[
                        'name'=>[
                            'settings'=>[
                                'mutate'=>function () {
                                    return 'foobar';
                                }
                            ]
                        ]
                    ]
                ]
            ],
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
                    'allowed'=>true,
                    'extends'=>[':default:create']
                ],
            ],
        ];


    }

}