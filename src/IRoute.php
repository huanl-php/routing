<?php

namespace HuanL\Routing;
interface IRoute {

    /**
     * 注册一个get路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public function get(string $uri, $action);

    /**
     * 注册一个post路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public function post(string $uri, $action);

    /**
     * 注册一个put路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public function put(string $uri, $action);

    /**
     * 注册一个delete路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public function delete(string $uri, $action);

    /**
     * 注册一个options路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public function options(string $uri, $action);

    /**
     * 注册所有的路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public function any(string $uri, $action);

}