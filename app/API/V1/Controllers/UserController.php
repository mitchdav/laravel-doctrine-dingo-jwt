<?php

namespace App\API\V1\Controllers;

use App\API\V1\Repositories\UserRepository;
use App\API\V1\Transformers\UserTransformer;
use TempestTools\Scribe\Orm\Transformers\ToArrayTransformer;

/** @noinspection LongInheritanceChainInspection */
class UserController extends APIControllerAbstract
{

    public function __construct(UserRepository $repo, ToArrayTransformer $arrayTransformer)
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
        // No special rules for this controller
        return [
            'default'=>[
                'GET'=>[],
                'POST'=>[],
                'PUT'=>[],
                'DELETE'=>[]
            ]
        ];
    }

    /**
     * Includes a special me action to get info about the currently logged in user (default functionality of the skeleton)
     * @return \Dingo\Api\Http\Response
     */
    public function me()
	{
		return $this->response->item($this->getUser(), new UserTransformer());
	}
}