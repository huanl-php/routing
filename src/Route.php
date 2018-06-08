<?php


namespace HuanL\Routing;

use Closure;

class Route {

    /**
     * 请求方式
     * @var array
     */
    private $method = [];

    /**
     * uri
     * @var string
     */
    private $uri = '';

    /**
     * 操作器
     * @var mixed
     */
    private $action = null;

    /**
     * 路由名
     * @var string
     */
    private $name = '';

    /**
     * uri参数名数组
     * @var array
     */
    private $paramList = [];

    /**
     * uri中的参数数组
     * @var array
     */
    private $paramValue = [];

    /**
     * 类方法
     * @var string
     */
    private $classMethod = '';

    /**
     * 控制器
     * @var mixed
     */
    private $controller = null;

    /**
     * 命名空间
     * @var string
     */
    private $namespace = '';

    public function __construct(array $method, $uri = '', $action = '') {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getUri(): string {
        return $this->uri;
    }

    /**
     * 获取路由的action操作
     * @return mixed
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * 执行路由的action操作
     * @param string $namespace
     * @return Closure|mixed
     */
    public function execAction() {
        //判断是否为匿名函数,如果是匿名函数直接返回
        //然后判断命名空间是否为空为空直接返回
        //不为空添加上再返回,如果都是空的直接返回
        if ($this->action instanceof Closure) {
            return $this->action;
        }
        //判断action是不是只是一个类名
        //如果只是一个类名,在参数中查找有没有action这个参数的存在
        //如果action存在,那么可以带上这个参数
        $retAction = $this->action;
        $param = $this->getParam();
        if (!empty($this->namespace)) {
            $retAction = $this->namespace . '\\' . $retAction;
        }
        //判断有没有@,如果有,处理这个,设置控制器和操作
        //然后直接返回
        if ($atPos = strpos($retAction, '@')) {
            $this->controller = substr($retAction, 0, $atPos);
            $this->classMethod = substr($retAction, $atPos + 1);
            return $retAction;
        }
        if (class_exists($retAction)) {
            //是一个类并且存在action这个参数
            if (isset($param['action'])) {
                $this->controller = $retAction;
                $this->classMethod = $param['action'];
                $retAction = $this->controller . '@' . $this->classMethod;
            }
        } else {
            //不是一个类,判断有没有controller和action参数
            if (isset($param['controller']) && isset($param['action'])) {
                $this->controller = $retAction . $param['controller'] . 'Controller';
                $this->classMethod = $param['action'];
                $retAction = $this->controller . '@' . $this->classMethod;
            }
        }
        return $retAction;
    }

    /**
     * 获取控制器
     * @return mixed
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * 获取调用的类方法名
     * @return string
     */
    public function getClassMethod(): string {
        return $this->classMethod;
    }

    /**
     * @return array
     */
    public function getMethod(): array {
        return $this->method;
    }

    /**
     * 设置路由名字
     * @param string $name
     * @return Route
     */
    public function name(string $name): Route {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * 与传入的url匹配
     * @param string $domain
     * @param string $url
     * @return bool|Route
     */
    public function match(string $domain, string $url) {
        //首先分析uri是否与url匹配,并提取出参数,暂时没准备写子域名的
        return $this->analyzeUrl($url);
    }

    /**
     * 解析url
     * @param string $url
     * @return $this|bool
     */
    public function analyzeUrl(string $url) {
        $regex = '^' . $this->dealUri();
        if (sizeof($this->paramList) <= 0) {
            $regex .= '$';
        }
        if (!preg_match("|$regex|", $url, $matches)) {
            return false;
        }
        if (sizeof($matches) - 1 !== sizeof($this->paramList)) {
            return false;
        }
        for ($i = 1; $i < sizeof($matches); $i++) {
            $this->paramList[$i - 1]['value'] = $matches[$i];
        }
        return $this;
    }

    /**
     * 获取解析url后得到的参数,需要先调用analyzeUrl才能获取到参数
     * @return array
     */
    public function getParam(): array {
        if ($this->paramValue == []) {
            foreach ($this->paramList as $item) {
                $this->paramValue[$item['name']] = $item['value'];
            }
        }
        return $this->paramValue;
    }

    /**
     * 处理uri,转换成正则的形式,并提取出参数
     * @return string
     */
    public function dealUri(): string {
        $this->paramList = [];
        $regex = preg_replace_callback([
            '|{(\w+?)}|',
            '|{(\w+?)\?}|'
        ], function ($match) {
            $this->paramList[]['name'] = $match[1];
            if (substr($match[0], strlen($match[0]) - 2, 1) == '?') {
                return '(\w*)';
            }
            return '(\w+)';
        }, $this->uri);
        return $regex;
    }

    /**
     * 设置命名空间
     * @param string $namespace
     * @return Route
     */
    public function setNamespace(string $namespace): Route {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * 返回命名空间
     * @return string
     */
    public function getNamespace(): string {
        return $this->namespace;
    }
}

