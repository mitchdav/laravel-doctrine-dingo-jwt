<?php

namespace App\API\V1\Controllers;

use Dingo\Api\Exception\ValidationHttpException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TempestTools\Moat\Contracts\HasIdContract;
use TempestTools\Common\Contracts\HasUserContract;
use TempestTools\Common\Laravel\Controller\BaseControllerAbstract;

use App;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class APIControllerAbstract extends BaseControllerAbstract implements HasUserContract
{
	use Helpers, ValidatesRequests;

    /**
     * APIControllerAbstract constructor.
     */
    public function __construct()
	{
		App::register(App\API\V1\Providers\APIServiceProvider::class);
		parent::__construct();
	}

    /**
     * @return HasIdContract
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
	public function getUser():?HasIdContract
	{
		try
		{
			if(($user = JWTAuth::parseToken()->authenticate()) === FALSE)
			{
				throw new UnauthorizedHttpException('user_not_found');
			}
		} catch(TokenExpiredException $e)
		{
			throw new UnauthorizedHttpException('token_expired');
		} catch(TokenInvalidException $e)
		{
			throw new UnauthorizedHttpException('token_invalid');
		} catch(JWTException $e)
		{
			throw new BadRequestHttpException('token_absent');
		}
		
		return $user;
	}
	/** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @throws \Dingo\Api\Exception\ValidationHttpException
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
	{
		$validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

		if($validator->fails())
		{
			throw new ValidationHttpException($validator->failed());
		}
	}
	/** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param array $array
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @throws \Dingo\Api\Exception\ValidationHttpException
     */
    public function validateArray(array $array, array $rules, array $messages = [], array $customAttributes = [])
	{
		$validator = $this->getValidationFactory()->make($array, $rules, $messages, $customAttributes);

		if($validator->fails())
		{
			throw new ValidationHttpException($validator->failed());
		}
	}
}