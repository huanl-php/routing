<?php


namespace HuanL\Routing;


class Route implements IRoute {
    /**
     * 注册的路由列表
     * @var array
     */
    private $routes = [];

    /**
     * 匹配路由
     * @param array|string $methods
     * @param string $uri
     * @param mixed $action
     * @return mixed|void
     */
    public static function match($methods, $uri, $action) {
        // TODO: Implement match() method.

    }


}