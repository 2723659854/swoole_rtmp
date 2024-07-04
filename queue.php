<?php

/**
 * @purpose 测试线程之间通信
 * @note 如果是要两个线程单独通信，那么就给两个线程单独创建两个queue，单向通信
 * 可以使用次实例实现异步队列
 * @note 另外，已经测试过了，channel不能在线程里面用
 */

use Swoole\Thread;
use Swoole\Thread\Queue;
/** 线程参数 */
$args = Thread::getArguments();
/** 线程总数 */
$c = 7;
/** 推送有效数据总数 */
$n = 60;
/** 主进程 */
if (empty($args)) {
    /** 创建子线程 */
    $threads = [];
    $queue = new Queue;
    for ($i = 0; $i < $c; $i++) {
        $threads[] = new Thread(__FILE__, $i, $queue);
    }
    /** 启动子线程 */
    for ($i = 0; $i < $c; $i++) {
        $threads[$i]->join();
    }
    var_dump($queue->count());
    /** 所有线程都执行完成后，主进程退出任务 */
} else {
    /** 以下是线程的业务 */
    $id = $args[0];
    $queue = $args[1];
    /** 第一个线程 */
    if ($id == 0) {
        while ($n--) {
            $queue->push(base64_encode(random_bytes(16)), Queue::NOTIFY_ONE);
            usleep(random_int(10000, 100000));
        }
        /** 这里必须是大于等于线程数，否则后面有线程接收不到空值，导致子线程无法退出，从而导致主进程一致挂起 */
        /** 当前线程不需要接收，所以少一个，当前线程投递完后，直接退出 */
        $n = $c-1;
        while ($n--) {
            var_dump("投递一个空值");
            $queue->push('', Queue::NOTIFY_ONE);
        }

        var_dump("数据投递完成");
    } else {
        /** 其他线程负责接受数据 */
        while (1) {
            $job = $queue->pop(-1);
            if (!$job) {
                /** 没有数据，则退出线程 */
                break;
            }
            var_dump("线程ID" . $id, $job);
        }
    }
}

