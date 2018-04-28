<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\CORSProvider;

use FastD\Http\Response;
use FastD\Middleware\DelegateInterface;
use FastD\Middleware\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CorsMiddleware extends Middleware
{
    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $next
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request, DelegateInterface $next)
    {
        $cors = app()->get('cors');

        if (!$cors->isCorsRequest($request)) {
            return $next->process($request);
        }

        if (!$cors->isOriginAllowed($request)) {
            return new Response('Not allowed in CORS policy.', Response::HTTP_FORBIDDEN);
        }

        return $cors->addActualRequestHeaders($request, $next->process($request));
    }
}
