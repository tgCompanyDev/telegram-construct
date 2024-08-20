<?php

namespace Valibool\TelegramConstruct\Services\Response;

use Illuminate\Http\Response;

/**
 *
 */
class ResponseService
{

    /**
     * @param $status
     * @param $code
     * @param $errors
     * @param $data
     * @return array
     */
    private static function responsePrams($status, $code, $errors = [], $data = [])
    {
        return [
            'status' => $status,
            'code' => $code,
            'errors' => (object)$errors,
            'data' => (object)$data,
        ];
    }

    /**
     * @param $status
     * @param $code
     * @param $errors
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendJsonResponse($status, $code = Response::HTTP_OK, $errors = [], $data = [])
    {
        return response()->json(
            self::responsePrams($status, $code, $errors, $data),
            $code
        );
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [])
    {
        return self::sendJsonResponse(true, Response::HTTP_OK, [], $data);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unSuccess($data = [])
    {
        return self::sendJsonResponse(false, Response::HTTP_OK, [], $data);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function сreated($data = [])
    {
        return self::sendJsonResponse(true, Response::HTTP_CREATED, [], $data);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function badRequest($data = [])
    {
        return self::sendJsonResponse(false, Response::HTTP_BAD_REQUEST, $data, []);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorized($data = [])
    {
        return self::sendJsonResponse(false, Response::HTTP_UNAUTHORIZED, $data, []);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function forbidden($data = [])
    {
        return self::sendJsonResponse(false, Response::HTTP_FORBIDDEN, $data, []);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound($data = [])
    {
        return self::sendJsonResponse(false, Response::HTTP_NOT_FOUND, $data, []);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unprocessableContent($data = [])
    {
        return self::sendJsonResponse(false, 422, [], $data);
    }
}
