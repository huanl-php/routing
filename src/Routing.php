<?php


namespace HuanL\Routing;


use HuanL\Container\Container;
use HuanL\Request\Request;
use Closure;

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
     * 容器
     * @var null
     */
    private $container = null;

    /**
     * 控制器文件
     * @var array
     */
    private $controllerFiles = [];

    /**
     * 路由群组
     * @var array
     */
    private $group = [];

    /**
     * 现行的群组
     * @var string
     */
    private $nowGroup = '';

    /**
     * Route constructor.
     * @param Request $request
     */
    public function __construct(Request $request, Container $container) {
        $this->request = $request;
        $this->container = $container;
        $this->routes = new Routes;
    }

    /**
     * get请求
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function get(string $uri, $action = ''): Route {
        // TODO: Implement get() method.
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * post请求
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function post(string $uri, $action = ''): Route {
        // TODO: Implement post() method.
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * put请求
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function put(string $uri, $action = ''): Route {
        // TODO: Implement post() method.
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * delete请求
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function delete(string $uri, $action = ''): Route {
        // TODO: Implement post() method.
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * options请求
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function options(string $uri, $action = ''): Route {
        // TODO: Implement post() method.
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * 所有操作
     * @param string $uri
     * @param mixed $action
     * @return Route
     */
    public function any(string $uri, $action = ''): Route {
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
    public function addRoute($method, $uri, $action = ''): Route {
        if (is_string($method)) {
            $method = [$method];
        }
        return $this->routes()->add($this->newRoute($method, $uri, $action));
    }

    /**
     * 导入路由
     * @param $routeArray
     */
    public function importRoute($routeArray) {
        $this->routes()->importRoute($routeArray);
    }

    /**
     * 导出路由
     * @param bool $isExportObject
     * @return array
     */
    public function exportRoute(bool $isExportObject = true) {
        return $this->routes()->exportRoute($isExportObject);
    }

    /**
     * 取得现行操作的routes
     * @return Routes
     */
    private function routes() {
        if (empty($this->nowGroup)) {
            return $this->routes;
        }
        return $this->group[$this->nowGroup];
    }

    /**
     * 实例化一个路由对象
     * @param $method
     * @param $uri
     * @param $action
     * @return Route
     */
    public function newRoute($method, $uri, $action): Route {
        return new Route($method, $uri, $action);
    }

    /**
     * 通过名字获取uri
     * @param $key
     * @return bool|string
     */
    public function name($key) {
        $name = $this->routes->name($key);
        if ($name !== false) {
            return $name;
        }
        foreach ($this->group as $item) {
            if (($name = $item->name($key)) !== false) {
                break;
            }
        }
        return $name;
    }

    /**
     * 解析控制器路由文件
     * @param string $path
     * @param string $suffix
     */
    public function resolveControllerFile(string $path, string $suffix = 'Controller') {
        //先搜索出控制器文件
        $this->searchControllerFile($path, $suffix);
        //然后通过正则匹配路由规则
        foreach ($this->controllerFiles as $file) {
            //读取文件内容,然后正则匹配
            //先匹配出命名空间,如果命名空间为空的就认为是错误的
            $content = file_get_contents($file);
            //匹配命名空间
            $pos = strpos($content, 'namespace ') + 10;
            $namespace = substr($content, $pos, strpos($content, "\n", $pos) - $pos - 2);
            preg_match_all(
                '|/\*\*[\s\S]*?\* @route (.*?)[\s]\n[\s\S]*?\*/[\s]*?public function (.*?)\(|',
                $content, $matches, PREG_SET_ORDER
            );
            foreach ($matches as $value) {
                $pos = strrpos($file, '/') + 1;
                $method = substr($file, $pos, strpos($file, '.php') - $pos)
                    . '@' . $value[2];
                $this->parameterAddRoute($value[1],
                    $namespace . '\\' . $method,
                    $method
                );
            }
        }
    }

    /**
     * 解析控制器注释参数添加到路由
     * @param $param
     * @param $action
     * @param $name
     * @throws RouteParameterException
     */
    private function parameterAddRoute($param, $action, $name) {
        $param = explode(' ', $param);
        switch (sizeof($param)) {
            case 1:
                $route = $this->any($param[0], $action);
                break;
            case 2:
                $route = $this->addRoute(explode(',', strtoupper($param[0])), $param[1], $action);
                break;
            default:
                throw new RouteParameterException();
        }
        $route->name($name);
    }

    /**
     * 搜索控制器文件
     * @param string $path
     * @param string $suffix
     */
    private function searchControllerFile(string $path, string $suffix = 'Controller') {
        $dir = opendir($path);
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..') {
                continue;
            } else {
                $file = $path . '/' . $file;
                if (strpos($file, "$suffix.php") !== false) {
                    $this->controllerFiles[] = $file;
                } else if (is_dir($file)) {
                    $this->searchControllerFile($file, $suffix);
                }
            }
        }
    }

    /**
     * 路由群组
     * @param  string $name
     * @param Closure $method
     * @param  array $parameteres
     * @return Routes
     */
    public function group(string $name, Closure $method, array $parameteres = []) {
        $route = new Routes();
        $route->setNamespace($parameteres['namespace'] ?? '');
        $this->group[$name] = $route;
        //如果是匿名函数,调用匿名方法
        if ($method instanceof Closure) {
            $this->nowGroup = $name;
            $method($this);
            $this->nowGroup = '';
        }
        return $route;
    }

    /**
     * 解析路由
     * @return mixed
     */
    public function resolve() {
        $route = $this->routes->findRoute($this->request);
        if ($route === false) {
            foreach ($this->group as $item) {
                $route = $item->findRoute($this->request);
                if ($route !== false) {
                    break;
                }
            }
            if ($route === false) {
                return false;
            }
        }
        $action = $route->execAction();
        if (!empty($route->getController())) {
            //如果控制器不是空的,注入一个controller
            $this->container->singleton($route->getController());
            //设置controller别名
            $this->container->alias('controller', $route->getController());
        }
        $this->container->instance(Route::class, $route);
        return $this->container->call($action, $route->getParam());
    }
}
