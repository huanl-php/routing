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

    public function add(Route $routing) {
        foreach ($routing->getMethod() as $value) {
            $this->routes[$value][$routing->getUri()] = $routing;
        }
        return $routing;
    }


    /**
     * 通过name获取uri
     * @param string $key
     * @return bool
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
     * 寻找url匹配的route
     * @param string $url
     */
    public function findRoute(Request $request) {
        $methodRoute = $this->routes[$request->method()] ?? [];
        foreach ($methodRoute as $item) {
            if ($item->match($request->domain(), $request->path_info()) === true) {
                return true;
            }
        }
        return false;
    }
}