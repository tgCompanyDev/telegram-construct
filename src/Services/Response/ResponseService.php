<?php

namespace Valibool\TelegramConstruct\Services\Response;

use Illuminate\Http\Response;

/**
 *
 */
class ResponseService
{

    /**
     * @param $errors
     * @param $data
     * @return array
     */
    private static function responsePrams($errors = [], $data = [])
    {
        return [
            'errors' => (object)$errors,
            'data' => (object)$data,
        ];
    }

    /**
     * @param $errors
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendJsonResponse($code = Response::HTTP_OK, $errors = [], $data = [])
    {
        return response()->json(
            self::responsePrams($errors, $data),
            $code
        );
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [])
    {
        return self::sendJsonResponse(Response::HTTP_OK, [], $data);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unSuccess($data = [])
    {
        return self::sendJsonResponse(Response::HTTP_NO_CONTENT, [], $data);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function сreated($data = [])
    {
        return self::sendJsonResponse(Response::HTTP_CREATED, [], $data);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function badRequest($data = [])
    {
        return self::sendJsonResponse(Response::HTTP_BAD_REQUEST, $data, []);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorized($data = [])
    {
        return self::sendJsonResponse( Response::HTTP_UNAUTHORIZED, $data, []);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function forbidden($data = [])
    {
        return self::sendJsonResponse( Response::HTTP_FORBIDDEN, $data, []);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound($data = [])
    {
        return self::sendJsonResponse( Response::HTTP_NOT_FOUND, $data, []);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unprocessableContent($data = [])
    {
        return self::sendJsonResponse( 422, [], $data);
    }
}
