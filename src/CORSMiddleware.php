<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\CORS;


use FastD\Middleware\DelegateInterface;
use FastD\Middleware\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CORSMiddleware extends Middleware
{
    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $next
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request, DelegateInterface $next)
    {
        $config = config()->get('cors', [
            'allow_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD'],
            'allow_origins' => ['*'],
            'allow_credentials' => 'true'
        ]);

        return $next->process($request)
            ->withHeader('Access-Control-Allow-Origin', implode(',', $config['allow_origins']))
            ->withHeader('Access-Control-Allow-Method', implode(',', $config['allow_methods']))
            ->withHeader('Access-Control-Allow-Credentials', $config['allow_credentials'])
            ;
    }
}