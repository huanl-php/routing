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
     * 命名空间
     * @var string
     */
    private $namespace = '';

    public function __construct(array $method, $uri, $action) {
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
     * 返回路由操作
     * @param string $namespace
     * @return Closure|mixed
     */
    public function getAction() {
        //判断是否为匿名函数,如果是匿名函数直接返回
        //然后判断命名空间是否为空为空直接返回
        //不为空添加上再返回,如果都是空的直接返回
        if ($this->action instanceof Closure) {
            return $this->action;
        } else if (!empty($this->namespace)) {
            return $this->namespace . '//' . $this->action;
        }
        return $this->action;
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
        $regex = '^'.$this->dealUri();
        if (sizeof($this->paramList)<=0){
            $regex.='$';
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
        $param = [];
        foreach ($this->paramList as $item) {
            $param[$item['name']] = $item['value'];
        }
        return $param;
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