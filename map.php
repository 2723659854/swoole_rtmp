<?php
use Swoole\Thread;
/** 使用map传递数据 */
use Swoole\Thread\Map;

$args = Thread::getArguments();
if (empty($args)) {
    $map = new Map;
    $thread = new Thread(__FILE__, 2, $map);
    sleep(1);
    $class = new stdClass();
    $class->name = "张三556565";
    $map['test'] = $class;
    $thread->join();
} else {
    $map = $args[1];
    sleep(2);
    var_dump($map['test']);
}
