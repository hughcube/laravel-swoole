<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:14.
 */

namespace HughCube\Laravel\Swoole\Http;

use Illuminate\Http\Request as IlluminateRequest;
use Swoole\Http\Request as SwooleRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends IlluminateRequest
{
    public static function createFromSwoole(SwooleRequest $swooleRequest)
    {
        /** @var array $query The GET parameters */
        $query = empty($swooleRequest->get) ? [] : $swooleRequest->get;

        /** @var array The POST parameters */
        $request = empty($swooleRequest->post) ? [] : $swooleRequest->post;

        /** @var array $attributes The request attributes (parameters parsed from the PATH_INFO, ...) */
        $attributes = [];

        /** @var array $cookies The COOKIE parameters */
        $cookies = empty($swooleRequest->cookie) ? [] : $swooleRequest->cookie;

        /** @var array $files The FILES parameters */
        $files = empty($swooleRequest->files) ? [] : $swooleRequest->files;

        /** @var array $header The http header parameters */
        $header = empty($swooleRequest->header) ? [] : $swooleRequest->header;

        /** @var array $server The SERVER parameters */
        $server = empty($swooleRequest->server) ? [] : $swooleRequest->server;
        $server = static::makeServerParameters($server, $header);

        /** @var null|string $content The raw body data */
        $content = $swooleRequest->rawContent();

        // Initialize laravel request
        IlluminateRequest::enableHttpMethodParameterOverride();

        $symfonyRequest = new SymfonyRequest($query, $request, $attributes, $cookies, $files, $server, $content);
        $request = IlluminateRequest::createFromBase($symfonyRequest);

        if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return $request;
    }

    /**
     * @param array $server
     * @param array $header
     * @return array
     */
    protected static function makeServerParameters(array $server, array $header)
    {
        $phpServer = [];

        foreach ($server as $key => $value) {
            $key = strtoupper($key);
            $phpServer[$key] = $value;
        }

        foreach ($header as $key => $value) {
            $key = str_replace('-', '_', $key);
            $key = strtoupper($key);

            if (!in_array($key, ['REMOTE_ADDR', 'SERVER_PORT', 'HTTPS'])) {
                $key = 'HTTP_' . $key;
            }

            $phpServer[$key] = $value;
        }

        return $phpServer;
    }
}
