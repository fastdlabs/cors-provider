<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\CORSProvider;

use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;
use FastD\Http\Response;
use FastD\Http\ServerRequest;

class CorsServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return mixed
     */
    public function register(Container $container)
    {
        route()->addRoute('OPTIONS', '/*', function (ServerRequest $request) {
            if (!app()->get('cors')->validatePreflightRequest($request)) {
                return new Response('Cors Request Not Allowed.', Response::HTTP_FORBIDDEN);
            }

            return app()->get('cors')->handlePreflightRequest($request);
        });
        $container->add('cors', new Cors(load(app()->getPath().'/config/cors.php')));
    }
}
