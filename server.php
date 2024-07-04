<?php
use Swoole\Process;
use Swoole\Thread;
use Swoole\Http\Server;

$http = new Server("0.0.0.0", 9503, SWOOLE_THREAD);
$http->set([
    'worker_num' => 2,
    'task_worker_num' => 3,
    'bootstrap' => __FILE__,
    // 通过init_arguments实现线程间的数据共享。
    'init_arguments' => function () use ($http) {
        $map = new Swoole\Thread\Map;
        return [$map];
    }
]);

$http->on('Request', function ($req, $resp) use ($http) {
    $resp->end('hello world');
});

$http->on('pipeMessage', function ($http, $srcWorkerId, $msg) {
    echo "[worker#" . $http->getWorkerId() . "]\treceived pipe message[$msg] from " . $srcWorkerId . "\n";
});

$http->addProcess(new Process(function () {
    echo "user process, id=" . Thread::getId();
    sleep(2000);
}));

$http->on('Task', function ($server, $taskId, $srcWorkerId, $data) {
    var_dump($taskId, $srcWorkerId, $data);
    return ['result' => uniqid()];
});

$http->on('Finish', function ($server, $taskId, $data) {
    var_dump($taskId, $data);
});

$http->on('WorkerStart', function ($serv, $wid) {
    // 通过Swoole\Thread::getArguments()获取配置中的init_arguments传递的共享数据
    var_dump(Thread::getArguments(), $wid);
});

$http->on('WorkerStop', function ($serv, $wid) {
    var_dump('stop: T' . Thread::getId());
});

$http->start();
