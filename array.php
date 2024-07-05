<?php

use Swoole\Thread;
/** 使用数组传递数据 */
use Swoole\Thread\ArrayList;

$args = Thread::getArguments();
if (empty($args)) {
    var_dump("主线程");
    var_dump(Swoole\Thread::getTsrmInfo());
    $list = new ArrayList;
    $thread = new Thread(__FILE__, 1, $list);
    sleep(1);
    $class = new stdClass();
    $class->name = "张三";

    $list[] = $class;
    $thread->join();
} else {
    var_dump("子线程");
    var_dump(Swoole\Thread::getTsrmInfo());
    $list = $args[1];
    sleep(2);
    var_dump($list[0]);
}
