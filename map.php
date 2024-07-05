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
    $map->add('demo','lucy');
    var_dump("子线程开始执行");
    $thread->join();
    var_dump("子线程执行完成");
} else {
    $map = $args[1];
    sleep(2);
    var_dump($map['test']);
    var_dump($map['demo']);
    var_dump($map->keys());
    var_dump($map->count());
    /** values方法报错 Fatal error: Uncaught Error: Call to undefined method Swoole\Thread\Map::values() in /var/www/swoole/map.php:23*/
    //var_dump($map->values());
    /** toArray方法报错 */
    //var_dump($map->toArray());
    var_dump($map->clean());
    var_dump($map->keys());

}
