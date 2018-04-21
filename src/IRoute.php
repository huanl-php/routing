<?php


namespace HuanL\Routing;


interface IRoute {

    /**
     * 注册一个路由
     * @param array|string $methods
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public static function match($methods, $uri, $action);

    /**
     * 注册一个get路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public static function get($uri, $action);

    /**
     * 注册一个post路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public static function post($uri, $action);

    /**
     * 注册一个put路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public static function put($uri, $action);

    /**
     * 注册一个delete路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public static function delete($uri, $action);

    /**
     * 注册一个option路由
     * @param string $uri
     * @param mixed $action
     * @return mixed
     */
    public static function option($uri, $action);

}
