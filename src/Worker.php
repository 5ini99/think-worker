<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\worker;

use think\Exception;
use think\Config;
use think\Lang;
use Workerman\Worker AS Workers;

/**
 * Worker控制器扩展类
 */
class Worker
{
    /**
     * @var object 对象实例
     */
    protected static $instance;

    protected $worker;
    protected $socket = '';
    protected $protocol = 'http';
    protected $host = '0.0.0.0';
    protected $port = '2346';
    protected $processes = 4;

    // 统计监控服务
    public $statistic_server = false;

    // Hook扩展方法
    protected static $hook = [];

    /**
     * 架构函数
     * Worker constructor.
     * @param null $socket
     */
    protected function __construct($socket = null)
    {
        // 实例化 Websocket 服务
        $this->worker = new Workers($socket ?: $this->protocol . '://' . $this->host . ':' . $this->port);
        // 设置进程数
        $this->worker->count = $this->processes;
        // 初始化
        $this->init();

        // 设置回调
        foreach ([
                     'onWorkerStart',
                     'onConnect',
                     'onMessage',
                     'onClose',
                     'onError',
                     'onBufferFull',
                     'onBufferDrain',
                     'onWorkerStop',
                     'onWorkerReload'
                 ] as $event) {
            if (method_exists($this, $event)) {
                $this->worker->$event = [$this, $event];
            }
        }
        // Run worker
        if (!defined('GLOBAL_START')) {
            Workers::runAll();
        }
    }

    protected function init()
    {
    }

    /**
     * 手动载入所需类库
     * @param $dir
     */
    public static function load($dir)
    {
        // 手动载入函数库
        if (file_exists($dir . '/common.php')) {
            require_once $dir . '/common.php';
        }

        // 手动载入语言包
        if (Config::get('lang_switch_on')) {
            Lang::load([
                $dir . DS . 'lang' . DS . request()->langset() . EXT
            ]);
        }

        // 手动载入配置文件
        if (file_exists($dir . DS . 'config.php')) {
            Config::load($dir . DS . 'config.php');
        }
    }

    public function __call($method, $args)
    {
        if (array_key_exists($method, self::$hook)) {
            array_unshift($args, $this);
            return call_user_func_array(self::$hook[$method], $args);
        } else {
            throw new Exception('method not exists:' . __CLASS__ . '->' . $method);
        }
    }

    /**
     * Hook 方法注入
     * @access public
     * @param string|array $method 方法名
     * @param mixed $callback callable
     * @return void
     */
    public static function hook($method, $callback = null)
    {
        if (is_array($method)) {
            self::$hook = array_merge(self::$hook, $method);
        } else {
            self::$hook[$method] = $callback;
        }
    }

    /**
     * 初始化
     * @access public
     * @param string $socket
     * @return \think\Request
     */
    public static function instance($socket = null)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($socket);
        }
        return self::$instance;
    }

    /**
     * 创建一个worker
     * @param null $socket
     * @return object|static
     */
    public static function create($socket = null)
    {
        self::$instance = new static($socket);
        return self::$instance;
    }

    public function __set($name, $value)
    {
        $this->worker->$name = $value;
    }

    public function __get($name)
    {
        return isset($this->worker->$name) ? $this->worker->$name : null;
    }

    public function __isset($name)
    {
        return isset($this->worker->$name);
    }
}
