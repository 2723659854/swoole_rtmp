<?php
/**
 * @purpose 尽可能的使用CPU的性能，一个进程配置和CPU内核数一样的线程，确保每一个CPU都被利用，然后考虑到线程中的任务可能是阻塞的，那么线程内使用
 * 协程。让协程自动进行io切换。这样子可以实现超高的并发。
 * @note 我们在这里使用http请求进行测试
 */

use Swoole\Thread;
use Swoole\Coroutine\Http\Client;
use function Swoole\Coroutine\run;

/** 时间戳 */
function timestamp()
{
    return (microtime(true));
}

/** 如果是主线程，$args为空，如果是子线程，$args不为空 */
$args = Thread::getArguments();
/** CPU内核数 */
//$cpuNumber = swoole_cpu_num();
/** 真实的物理机的内核是14 */
$cpuNumber = 14;
/** 请求次数 */
$requestNumber = 100;
/** 请求地址 */
$host = "www.baidu.com";
/** 请求端口 */
$port = 80;
/** 实际请求次数 */
$sum = $cpuNumber * $requestNumber;
/** 获取当前内存使用 */
$peak_memory_usage = memory_get_peak_usage();
echo "开始内存使用峰值：" . $peak_memory_usage . " 字节\n";
/** 这外面的代码会被每一个线程执行，那么说明线程会继承主进程的数据 */
if (empty($args)) {
    /** 这里面是主进程，代码只会执行一次，当所有的线程的任务执行完成后才会退出 */
    $start = timestamp();
    /** 主线程创建线程 */
    for ($i = 0; $i < $cpuNumber; $i++) {
        $threads[] = new Thread(__FILE__, $i);
    }
    /** 启动线程任务 */
    for ($i = 0; $i < $cpuNumber; $i++) {
        $threads[$i]->join();
    }
    $end = timestamp();
    $peak_memory_usage1 = memory_get_peak_usage();
    echo "结束内存使用峰值：" . $peak_memory_usage1 . " 字节\n";
    var_dump("{$sum}次请求一共花费了时间：" . ($end - $start) . "秒");
    /** 主进程挂起 等待线程执行完所有任务 */
} else {
    /** 每一个线程分别发送100个http请求 */
    for ($j = 0; $j <= $requestNumber; $j++) {
        /** 子线程里面套协程 协程里面执行异步任务 */
        run(function () use ($host, $port) {
            $client = new Client($host, $port);
            $client->setHeaders([
                'Host' => $host,
                'User-Agent' => 'Chrome/49.0.2587.3',
                'Accept' => 'text/html,application/xhtml+xml,application/xml',
                'Accept-Encoding' => 'gzip',
            ]);
            $client->set(['timeout' => 1]);
            $client->get('/');
            //var_dump($client->getBody());
            //var_dump($client->getStatusCode());
            //var_dump($client->getHeaders());
            //var_dump($client->getCookies());
            $client->close();
        });
    }
}
