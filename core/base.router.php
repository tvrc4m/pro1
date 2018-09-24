<?php

class Router extends Base{

    /**
     * 请求url
     * @var string
     */
    protected $action;

    /**
     * 路由匹配规则
     * @var array
     */
    protected $routes;

    /**
     * 保存当前的路径信息
     * @var array
     */
    protected $route;

    public function __construct($action){

        $this->action=strtolower($action);
        
        $this->routes=include_once(CONF.'routes.php');
    }

    public function match(){
        
        foreach ($this->routes as $route) {
            // 先过滤关联的关键词信息
            $regex=preg_replace('@:\w+@','', $route);
            // 按照优先级进行匹配
            if(preg_match($regex, $this->action,$matches)){
                // 匹配成功之后将关联的关键词提取出来
                preg_match_all('@:(\w+)@',$route, $keys);
                // 将完整匹配第一条去掉
                unset($matches[0]);
                $this->route=array_combine($keys[1], $matches);

                $group=$this->route['group']??'index';
                $controller=$this->route['controller']??'index';
                $classpath=API.$this->route['module'].'/'.$group.'/'.$controller.'.api.php';
                
                if(!file_exists($classpath)) break;

                return true;
            }
        }

        $this->error('未匹配到路由信息');
    }

    /**
     * 路由分发
     * @return
     */
    public function dispatch(){
        // 进行路由匹配
        $this->match();

        $module=$this->route['module'];
        $group=$this->route['group']??'index';
        $controller=$this->route['controller']??'index';
        $method=$this->route['method']??'index';

        $classpath=API.$module.'/'.$group.'/'.$controller.'.api.php';
        // $classpath=API.$module.'/'.$controller.'.api.php';

        // if(!file_exists($classpath)) $this->error('未找到指定类');
        // 
        if($module=='admin'){

            $params=array_merge($_GET,$_POST);
        }else{

            $params=json_decode(isset($_POST['data'])?decrypt($_POST['data']):"[]",true);
        }

        if($params===false || $params===null) $this->error('参数错误');

        $params['device']=$_REQUEST['device'];

        include_once $classpath;

        $classname=ucfirst($controller).'Api';

        $instance=new $classname($params);

        $instance->router=$this;
        
        // print_r($instance->router);exit;
        // unset($_POST['data']);
        
        // $params=array_merge($_GET,$_POST,$data);
        
        // $instance->call($params);

        if(!method_exists($instance, $method)) $this->error('未找到指定方法');
        
        call_user_func_array([$instance,$method], [$params]);
    }

    /**
     * 获取当前路由匹配的name值
     * @param  string $name 匹配到的值
     * @return mixed
     */
    public function __get($name){

        return $this->route[$name];
    }
}
