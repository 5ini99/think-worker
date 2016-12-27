<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: byron sampson <xiaobo.sun@qq.com>
// +----------------------------------------------------------------------

\think\Console::addDefaultCommands([
    "think\\worker\\command\\Start",
    "think\\worker\\command\\Restart",
    "think\\worker\\command\\Stop",
    "think\\worker\\command\\Kill"
]);

if (!function_exists('worker')) {

    /**
     * 添加到队列
     * @param        $job
     * @param string $data
     * @param int    $delay
     * @param null   $queue
     */
    function worker($job, $data = '', $delay = 0, $queue = null)
    {
        if ($delay > 0) {
            \think\Queue::later($delay, $job, $data, $queue);
        } else {
            \think\Queue::push($job, $data, $queue);
        }
    }
}