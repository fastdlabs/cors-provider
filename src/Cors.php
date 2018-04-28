<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2018-04
 */

namespace FastD\CORSProvider;

use FastD\Http\Response;
use FastD\Http\ServerRequest;

class Cors
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * Cors constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $this->formatConfig($config);
    }

    /**
     * @param ServerRequest $request
     * @return bool
     */
    public function isCorsRequest(ServerRequest $request)
    {
        if (!$request->hasHeader('origin')) {
            return false;
        }

        return $request->getHeader('origin')[0] === $this->getRequestSchemeAndHttpHost($request);
    }

    /**
     * @param ServerRequest $request
     * @return bool
     */
    public function isOriginAllowed(ServerRequest $request)
    {
        if (['*'] === $this->config['origins']) {
            return true;
        }

        $origin = $request->getHeader('origin')[0];

        return false !== array_search($origin, $this->config['origins'], true);
    }

    /**
     * @param ServerRequest $request
     * @param Response $response
     * @return Response
     */
    public function addActualRequestHeaders(ServerRequest $request, Response $response)
    {
        // setting Access-Control-Allow-Credentials
        $response->withAddedHeader(
            'Access-Control-Allow-Credentials',
            $this->config['allow_credentials'] ? 'true' : 'false'
        );

        // setting Access-Control-Allow-Origin
        // for requests without credentials(cookie), the server may specify "*" as a wildcard.
        // Note that cookies set in CORS responses are subject to normal third-party cookie policies.
        // see: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
        if (['*'] === $this->config['origins'] && !$this->config['allow_credentials']) {
            $response->withAddedHeader('access-control-allow-origin', '*');
        } else {
            $response->withAddedHeader('access-control-allow-origin', $request->getHeader('origin')[0]);

            // If the server specifies an origin host rather than "*",
            // then it could also include Origin in the Vary response header to indicate to clients
            // that server responses will differ based on the value of the Origin request header.
            $response->withAddedHeader('vary', 'Origin');
        }

        // setting Access-Control-Expose-Headers
        if ($this->config['exposed_headers']) {
            $response->withHeaders([
                'access-control-expose-headers' => $this->config['exposed_headers'],
            ]);
        }

        return $response;
    }

    /**
     * @param ServerRequest $request
     * @return bool
     */
    public function isPreflightRequest(ServerRequest $request)
    {
        return 'OPTIONS' === $request->getMethod() && $request->hasHeader('access-control-request-method');
    }

    /**
     * @param ServerRequest $request
     * @return bool
     */
    public function validatePreflightRequest(ServerRequest $request)
    {
        if (false === array_search($request->getHeader('access_control_request_method')[0], $this->config['methods'], true)) {
            return false;
        }

        if ($request->hasHeader('access_control_request_headers')) {
            if (['*'] !== $this->config['headers']
                && array_diff($request->getHeader('access_control_request_headers'), $this->config['headers'])
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ServerRequest $request
     * @return Response
     */
    public function handlePreflightRequest(ServerRequest $request)
    {
        $response = new Response();

        $response->withHeaders([
            'access-control-allow-methods' => $this->config['methods'],
        ]);

        if ($request->hasHeader('access_control_request_headers')) {
            $response->withHeaders([
                'access-control-allow-headers' => $request->getHeader('access_control_request_headers'),
            ]);
        }

        if (!is_null($this->config['max_age'])) {
            $response->withHeader('access-control-max-age', $this->config['max_age']);
        }

        return $response;
    }

    /**
     * @param ServerRequest $request
     * @return string
     */
    protected function getRequestSchemeAndHttpHost(ServerRequest $request)
    {
        $scheme = $request->getUri()->getScheme();
        $port = $request->getUri()->getPort();

        $return = "{$scheme}://" . $request->getUri()->getHost();

        if (('http' === $scheme && 80 !== $port) || ('https' === $scheme && 443 !== $port)) {
            $return .= ":{$port}";
        }

        return $return;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function formatConfig(array $config)
    {
        return [
            'allow_credentials' => (bool)($config['allow_credentials'] ?? true),
            'origins' => (array)($config['origins'] ?? '*'),
            'methods' => (array)($config['methods'] ?? ['GET', 'POST', 'HEAD', 'DELETE', 'PATCH', 'PUT']),
            'headers' => (array)($config['headers'] ?? '*'),
            'exposed_headers' => (array)($config['exposed_headers'] ?? []),
            'max_age' => $config['max_age'] ?? null,
        ];
    }
}
