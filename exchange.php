<?php
use Swoole\Thread;
/** 使用map传递数据交换 */
use Swoole\Thread\Map;

$args = Thread::getArguments();
if (empty($args)) {
    $map = new Map;
    /** 当这里被实例化后，子线程会立即执行 */
    $thread = new Thread(__FILE__, 2, $map);
    $map->add('username','lucy1');
    $map->add('age',28);
    $map->add('sex',"man");
    var_dump("子线程开始执行");
    /** 主线程等待子线程结束后才继续往下执行，如果不用这个函数，主线程会直接往下执行，但是主线程不会退出，当子线程执行完成后，退出 */
    $thread->join();
    var_dump("子线程执行完成",$map['res']);
} else {
    /** 子线程被创建后会立即执行 */
    /** 模拟一个子线程执行一个异步的任务，并返回结果 */
    $map = $args[1];
    /** 等待主线程投递数据，所以这里用一个循环判断是否已经投递了数据 */
    while(1){
        if (isset($map['username'])&&isset($map['age'])&&isset($map['sex'])){
            break;
        }
        /** 出让cpu，防止长时间占用影响其他任务 */
        usleep(1);
    }
    sleep(5);
    $string = $map['username'].$map['age'].$map['sex'];
    $map->add('res',$string);
    var_dump("投递计算结果完成");
}
