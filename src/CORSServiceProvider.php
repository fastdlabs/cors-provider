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

class CORSServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return mixed
     */
    public function register(Container $container)
    {
        route()->addRoute('OPTIONS', '/*', function () {
            return new Response();
        });

        app()->get('dispatcher')->before(new CORSMiddleware());
    }
}