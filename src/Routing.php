<?php


namespace HuanL\Routing;


use HuanL\Request\Request;

class Routing implements IRoute {
    /**
     * 路由合集实例
     * @var Routes|null
     */
    private $routes = null;

    /**
     * 请求实例
     * @var Request|null
     */
    private $request = null;

    /**
     * 请求方式列表
     * @var array
     */
    private static $method = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * Route constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
        $this->routes = new Routes;
    }

    public function get(string $uri, $action = null) {
        // TODO: Implement get() method.
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, $action) {
        // TODO: Implement post() method.
        return $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, $action) {
        // TODO: Implement post() method.
        return $this->addRoute('PUT', $uri, $action);
    }

    public function delete(string $uri, $action) {
        // TODO: Implement post() method.
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function options(string $uri, $action) {
        // TODO: Implement post() method.
        return $this->addRoute('DELETE', $uri, $action);
    }

    public function any(string $uri, $action) {
        // TODO: Implement any() method.
        return $this->addRoute(static::$method, $uri, $action);
    }

    /**
     * 添加一个路由
     * @param $method
     * @param $uri
     * @param $action
     * @return Route
     */
    protected function addRoute($method, $uri, $action) {
        if (is_string($method)) {
            $method = [$method];
        }
        return $this->routes->add($this->newRoute($method, $uri, $action));
    }

    /**
     * 实例化一个路由对象
     * @param $method
     * @param $uri
     * @param $action
     * @return Route
     */
    public function newRoute($method, $uri, $action) {
        return new Route($method, $uri, $action);
    }

    /**
     * 通过名字获取uri
     * @param $key
     * @return bool
     */
    public function name($key) {
        return $this->routes->name($key);
    }

    /**
     * 解析路由
     * @return bool
     */
    public function resolve() {
        return $this->routes->findRoute($this->request);
    }

}
