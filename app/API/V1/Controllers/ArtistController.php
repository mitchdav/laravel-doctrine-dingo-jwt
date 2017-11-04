<?php

namespace App\API\V1\Controllers;

use App\API\V1\Repositories\ArtistRepository;
use TempestTools\Scribe\Contracts\Events\SimpleEventContract;
use TempestTools\Scribe\Orm\Transformers\ToArrayTransformer;

/** @noinspection LongInheritanceChainInspection */
class ArtistController extends APIControllerAbstract
{

    public function __construct(ArtistRepository $repo, ToArrayTransformer $arrayTransformer)
    {
        $this->setRepo($repo);
        $this->setTransformer($arrayTransformer);
        parent::__construct();
    }
    /** @noinspection SenselessMethodDuplicationInspection */

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'GET'=>[],
                'POST'=>[
                    'transformerSettings'=>[
                        'recompute'=>true
                    ]
                ],
                'PUT'=>[
                    'extends'=>[':default:POST']
                ],
                'DELETE'=>[
                    'extends'=>[':default:POST']
                ]
            ]
        ];
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onInit (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['arrayHelper']->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onInit'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreIndex (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPreIndex'] = $event->getEventArgs()->getArrayCopy();
    }


    /**
     * @param SimpleEventContract $event
     */
    public function onPostIndex (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPostIndex'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreStore (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPreStore'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPostStore (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPostStore'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreShow (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPreShow'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPostShow (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPostShow'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreUpdate (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPreUpdate'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPostUpdate (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['arrayHelper'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreDestroy (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPreDestroy'] = $event->getEventArgs()->getArrayCopy();
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPostDestroy (SimpleEventContract $event):void
    {
        $eventArgs = $event->getEventArgs();

        /** @noinspection NullPointerExceptionInspection */
        $array = $eventArgs['controller']->getArrayHelper()->getArray();
        if (!isset($array['controllerEvents'])) {
            $array['controllerEvents'] = [];
        }
        $array['controllerEvents']['onPostDestroy'] = $event->getEventArgs()->getArrayCopy();
    }

}
