<?php
$lock = new Swoole\Lock(SWOOLE_MUTEX);
echo "[Master]create lock\n";
$lock->lock();
if (pcntl_fork() > 0)
{
    /** 主进程 */
    sleep(1);
    $lock->unlock();
}
else
{
    /** 子进程 */
    echo "[Child] Wait Lock\n";
    $lock->lock();
    echo "[Child] Get Lock\n";
    $lock->unlock();
    exit("[Child] exit\n");
}
echo "[Master]release lock\n";
unset($lock);
sleep(1);
echo "[Master]exit\n";
