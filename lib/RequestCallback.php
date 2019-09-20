<?php

namespace Lib;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * onRequest 回调
 * Class RequestCallback
 * @package Lib
 */
class RequestCallback
{
    public function onRequest(Request $request, Response $response)
    {
        $result = '';
        try {
            $this->needResponseJson($request->header) && $response->header('Content-Type', 'application/json; charset=utf-8');
            $data = $this->parseRequestData($request);
            $router = new Router($request->server['request_uri'] ?? '');
            $result = $router->run($data);
        } catch (\Exception $e) {


            ($code = $e->getCode()) != 0 or $code = 400;
            $msg = Config::get('code.' . $code) ?? '失败';
            $result = ['code' => $code, 'msg' => $msg, 'data' => new \stdClass()];
        }

        is_array($result) && $result = json_encode($result);
        $response->end($result);
    }

    /**
     * @param array $header
     * @param string $rawContent
     * @return array
     */
    private function parseRequestData(Request $request): array
    {
        $data = [];
        switch (strtolower($request->server['request_method'] ?? '')) {
            case 'get':
                $data = $request->get ?? [];
                break;
            case 'post':
                $data = $this->parsePost(
                    $request->header['content-type'] ?? '',
                    $request->post,
                    $request->rawContent()
                );
                break;
            default:
                break;
        }
        unset($request);
        return $data;
    }

    /**
     * @param array $header
     * @return array
     */
    private function parsePost(string $contentType, $post, string $rawContent): array
    {
        $contentType = strtolower($contentType);
        if (stristr($contentType, 'application/json') !== false) {
            $data = json_decode($rawContent, true) ?? [];
        } else {
            $data = $post ?? [];
        }
        return $data;
    }

    private function needResponseJson(array $headers): bool
    {
        return stristr($headers['accept'] ?? '', 'json') !== false ||
            stristr($headers['content-type'] ?? '', 'application/json') !== false;
    }
}