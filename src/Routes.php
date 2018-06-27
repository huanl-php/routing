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
     * 命名空间
     * @var string
     */
    private $namespace = '';

    /**
     * 添加一个路由
     * @param Route $routing
     * @return Route
     */
    public function add(Route $routing): Route {
        foreach ($routing->getMethod() as $value) {
            $this->routes[$value][$routing->getUri()] = $routing;
        }
        return $routing->setNamespace($this->namespace);
    }

    /**
     * 通过name获取uri
     * @param string $key
     * @return bool|string
     */
    public function name(string $key) {
        if (isset($this->name[$key])) {
            return $this->name[$key];
        }
        //遍历
        foreach ($this->routes as $value) {
            foreach ($value as $item) {
                if ($item->getName() == $key) {
                    return $this->name[$key] = $item->getUri();
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

    /**
     * 通过数组导入路由
     * @param $routeArray
     */
    public function importRoute($routeArray) {
        foreach ($routeArray as $key => $item) {
            $this->add(new Route($item['method'], $key, $item['action']))->name($item['name']);
        }
    }

    /**
     * 导出路由数组
     * @param bool $isExportObject
     * @return array
     */
    public function exportRoute(bool $isExportObject = true) {
        //遍历一次路由数组,导出路由数组
        //可以将其缓存下来,方便下一次的加载
        $routeArray = [];
        foreach ($this->routes as $key => $value) {
            foreach ($value as $item) {
                //判断是否导出对象类型的操作
                if (!$isExportObject && !is_string($item->getAction()))
                    continue;

                $routeArray[$item->getUri()] = [
                    'action' => $item->getAction(),
                    'method' => $item->getMethod(),
                    'name' => $item->getName()
                ];
            }
        }
        return $routeArray;
    }

    /**
     * 设置命名空间
     * @param string $namespace
     * @return Routes
     */
    public function setNamespace(string $namespace): Routes {
        $this->namespace = $namespace;
        return $this;
    }
}