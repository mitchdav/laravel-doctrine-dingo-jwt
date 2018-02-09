<?php
namespace App\API\V1\Entities;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;
use TempestTools\Scribe\Doctrine\Events\GenericEventArgs;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\ArtistRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name_unq", columns={"name"})})
 * @ORM\HasLifecycleCallbacks
 */
class Artist extends EntityAbstract
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
     * @ORM\OneToMany(targetEntity="App\API\V1\Entities\Album", mappedBy="artist", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    private $albums;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->albums = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Artist
     */
    public function setId(int $id): Artist
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|NULL
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Artist
     */
    public function setName(string $name): Artist
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection|NULL
     */
    public function getAlbums(): ?Collection
    {
        return $this->albums;
    }

    /**
     * @param Album $album
     * @return Artist
     */
    public function addAlbum(Album $album): Artist
    {
        $this->albums[] = $album;
        $album->setArtist($this);
        return $this;
    }

    /**
     * @param Album $album
     * @return Artist
     */
    public function removeAlbum(Album $album): Artist
    {
        $this->albums->removeElement($album);
        return $this;
    }

    public function preToArray(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $array = $this->getArrayHelper()->getArray();
        if (!isset($array['entityEvents'])) {
            $array['entityEvents'] = [];
        }
        $array['entityEvents']['preToArray'] = $e->getArgs()->getArrayCopy();
    }

    public function postToArray(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $array = $this->getArrayHelper()->getArray();
        if (!isset($array['entityEvents'])) {
            $array['entityEvents'] = [];
        }
        $array['entityEvents']['postToArray'] = $e->getArgs()->getArrayCopy();
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
                        'albums'=>[],
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
            'admin'=>[ // can do everything in default, and is allowed to do it when a super admin
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
            'testNullAssignType'=>[ // can do everything in default, and is allowed to do it when a super admin
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                    'fields'=>[
                        'albums'=>[
                            'assign'=>[
                                'null'=>false
                            ]
                        ]
                    ]
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
            ],
            'testLazyLoadEnabled'=>[
                'create'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true,
                    'toArray'=> [
                        'albums'=>[
                            'allowLazyLoad'=>true
                        ],
                    ]
                ],
                'update'=>[
                    'extends'=>[':testLazyLoadEnabled:create'],
                ],
                'delete'=>[
                    'extends'=>[':testLazyLoadEnabled:create'],
                ],
                'read'=>[
                    'extends'=>[':testLazyLoadEnabled:create']
                ],
            ],
            'testLiteralToArray'=>[
                'create'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true,
                    'toArray'=> [
                        'literalString'=>[
                            'type'=>'literal',
                            'value'=>'bob\'s your uncle'
                        ],
                        'literalArrayExpression'=>[
                            'type'=>'literal',
                            'value'=>ArrayExpressionBuilder::closure(
                                function (array $params) {
                                    return $params['key'];
                                }
                            )
                        ],
                        'literalDateWithFormat'=>[
                            'type'=>'literal',
                            'value'=>new DateTime('2001-01-01'),
                            'format'=>'Y-m-d H:i:s'
                        ],
                    ]
                ],
                'update'=>[
                    'extends'=>[':testLiteralToArray:create'],
                ],
                'delete'=>[
                    'extends'=>[':testLiteralToArray:create'],
                ],
                'read'=>[
                    'extends'=>[':testLiteralToArray:create']
                ],
            ]
        ];
    }
}