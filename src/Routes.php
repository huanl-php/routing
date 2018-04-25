<?php


namespace HuanL\Routing;


use HuanL\Request\Request;

class Routes {

    /**
     * 路由列表
     * @var array
     */
    private $routes = [];

    /**
     * uri名称
     * @var array
     */
    private $name = [];

    /**
     * 添加一个路由
     * @param Route $routing
     * @return Route
     */
    public function add(Route $routing): Route {
        foreach ($routing->getMethod() as $value) {
            $this->routes[$value][$routing->getUri()] = $routing;
        }
        return $routing;
    }


    /**
     * 通过name获取uri
     * @param string $key
     * @return bool|string
     */
    public function name(string $key) {
        //遍历
        foreach ($this->routes as $value) {
            foreach ($value as $item) {
                if ($item->getName() == $key) {
                    return $item->getUri();
                }
            }
        }
        return false;
    }

    /**
     * 寻找url匹配的Route
     * @param Request $request
     * @return Route|bool
     */
    public function findRoute(Request $request) {
        $methodRoute = $this->routes[$request->method()] ?? [];
        foreach ($methodRoute as $item) {
            if (($route = $item->match($request->domain(), $request->path_info())) !== false) {
                return $route;
            }
        }
        return false;
    }
}