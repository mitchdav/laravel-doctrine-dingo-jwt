<?php

namespace App\API\V1\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use TempestTools\Scribe\Exceptions\Laravel\Controller\ControllerException;
use Config;

/** @noinspection LongInheritanceChainInspection */
class ContextController extends APIControllerAbstract
{


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $config = Config::all();
        $result = $config['contexts'];
        return response()->json($result);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \TempestTools\Scribe\Exceptions\Laravel\Controller\ControllerException
     */
    public function store(Request $request): JsonResponse
    {
        throw ControllerException::methodNotImplemented('store');
    }


    /**
     * @param Request $request
     * @param null $id
     * @return JsonResponse
     */
    public function show(Request $request, $id=null): JsonResponse
    {
        $config = Config::all();
        $result = $config['contexts'][$id];
        return response()->json($result);
    }


    /**
     * @param Request $request
     * @param null $id
     * @return JsonResponse
     * @throws \TempestTools\Scribe\Exceptions\Laravel\Controller\ControllerException
     */
    public function update(Request $request, $id=null): JsonResponse
    {
        throw ControllerException::methodNotImplemented('update');
    }


    /**
     * @param Request $request
     * @param null $id
     * @return JsonResponse
     * @throws \TempestTools\Scribe\Exceptions\Laravel\Controller\ControllerException
     */
    public function destroy(Request $request, $id = null): JsonResponse
    {
        throw ControllerException::methodNotImplemented('destroy');
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