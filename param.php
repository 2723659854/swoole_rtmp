<?php

/**
 * @purpose 测试线程之间数据
 * @note 线程之间的数据是隔离的，不会相互污染
 * @comment 假设一个业务场景：就比如是广播，有很多个客户端，需要将相同的数据推送给所有的客户端。客户端使用协程链接。
 * 一个子线程是rtmp服务，将rtmp的数据包投递到队列，另外一个子线程是flv服务，负责读取队列，然后将数据推送给所有的客户端链接。
 *
 * @note 可能需要使用map
 */

use Swoole\Thread;
use Swoole\Thread\Queue;

/** 线程参数 */
$args = Thread::getArguments();
/** 线程总数 */
$c = 7;
/** 推送有效数据总数 */
$n = 60;

$num = 1;
/** 主进程 */
if (empty($args)) {
    /** 创建子线程 */
    $threads = [];
    /** 主线程创建线程 */
    for ($i = 0; $i < $c; $i++) {
        $threads[] = new Thread(__FILE__, $i);
    }
    /** 启动子线程 */
    foreach ($threads as $thread){
        $thread->join();
    }
    /** 所有线程都执行完成后，主进程退出任务 */
} else {
    /** 以下是线程的业务 */
    $id = $args[0];
    var_dump($num++);
}

