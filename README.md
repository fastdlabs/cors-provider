# FastD Cors Provider

## Installation
```
composer require fastd/cors-provider
```

## Usage

复制 `vendor/fastd/cors-provider/config/cors.php` 至 `config/cors.php`, 并修改配置

```php
<?php
return [
    'allow_credentials' => true,
    'origins' => ['*'],
    'methods' => ['GET', 'POST', 'HEAD', 'DELETE', 'PATCH', 'PUT'],
    'headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => null,
];

```

配置 `config/routes.php`

```php
<?php
use FastD\CORSProvider\CorsMiddleware;
use \FastD\Routing\RouteCollection;

route()->middleware(new CorsMiddleware(), function (RouteCollection $router) {
    $router->get('/demo', function () {
        return json([
            'foo' => 'bar',
        ]);
    });
});
```

完成 !

执行 `curl -i 'http://127.0.0.1:8889/demo' -XGET`

响应:

```
HTTP/1.1 200
Host: 127.0.0.1:8889
Date: Sat, 28 Apr 2018 17:08:47 +0800
Connection: close
X-Powered-By: PHP/7.2.4
Content-Type: application/json; charset=UTF-8

{"foo":"bar"}
```

执行 `curl -i 'http://127.0.0.1:8889/demo' -XGET -H 'Origin: http://127.0.0.1:8889'`

响应:

```
HTTP/1.1 200
Host: 127.0.0.1:8889
Date: Sat, 28 Apr 2018 17:09:35 +0800
Connection: close
X-Powered-By: PHP/7.2.4
Content-Type: application/json; charset=UTF-8
Access-Control-Allow-Credentials: true
Access-Control-Allow-Origin: http://127.0.0.1:8889
Vary: Origin
Access-Control-Expose-Headers: mother-fuck,shit

{"foo":"bar"}
```

执行 `curl -i 'http://127.0.0.1:8889/demo' -XOPTIONS -H 'access-control-request-method: GET'`

响应:

```
HTTP/1.1 200
Host: 127.0.0.1:8889
Date: Sat, 28 Apr 2018 17:10:31 +0800
Connection: close
X-Powered-By: PHP/7.2.4
Access-Control-Allow-Methods: GET,POST,HEAD,DELETE,PATCH,PUT
Access-Control-Max-Age: 200
Content-type: text/html; charset=UTF-8
```

执行 `curl -i 'http://127.0.0.1:8889/demo' -XOPTIONS -H 'access-control-request-method: GET' -H 'access-control-request-headers: hello'`

响应:

```
HTTP/1.1 200
Host: 127.0.0.1:8889
Date: Sat, 28 Apr 2018 17:11:27 +0800
Connection: close
X-Powered-By: PHP/7.2.4
Access-Control-Allow-Methods: GET,POST,HEAD,DELETE,PATCH,PUT
Access-Control-Allow-Headers: hello
Access-Control-Max-Age: 200
Content-type: text/html; charset=UTF-8
```