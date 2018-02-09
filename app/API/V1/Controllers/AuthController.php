<?php

namespace App\API\V1\Controllers;

use Dingo\Api\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

use App;

class AuthController extends APIControllerAbstract
{

    /** @var JWTAuth $auth */
	protected  /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $auth;

	public function __construct()
	{
		parent::__construct();

		$this->auth = App::make(JWTAuth::class);
	}

	public function authenticate(Request $request)
	{
		$credentials = $request->only('email', 'password');
		
		try
		{
			if(($token = $this->auth->attempt($credentials)) === FALSE)
			{
				return response()->json(['error' => 'invalid_credentials'], 401);
			}
		} catch(JWTException $e)
		{
			return response()->json(['error' => 'could_not_create_token'], 500);
		}
		
		return response()->json(compact('token'));
	}
	
	public function refresh()
	{
		if($this->auth->getToken() === FALSE)
		{
			throw new BadRequestHttpException('token_absent');
		}
		
		try
		{
			$token = $this->auth->refresh();
		} catch(TokenInvalidException $e)
		{
			throw new UnauthorizedHttpException('token_invalid');
		}
		
		return response()->json(compact('token'));
	}

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'GET'=>[],
                'POST'=>[],
                'PUT'=>[],
                'DELETE'=>[]
            ]
        ];
    }
}
