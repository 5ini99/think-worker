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

namespace think;

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

    public static function __callStatic($name, $arguments)
    {
        //return call_user_func_array([self::buildConnector(), $name], $arguments);
    }
}
