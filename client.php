<?php
/** 这里是tcp通信，需要自己处理通信协议，因为发送的数据流， */
// 服务器地址和端口
$server = '127.0.0.1';
$port = 9502;

// 创建一个套接字
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

// 连接到服务器
$result = socket_connect($socket, $server, $port);
if ($result === false) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
}

echo "Connected to the server.\n";
// 设置超时时间，5秒读取超时
//socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 0));
// 要发送的消息
$message = "Hello, Server!";
socket_write($socket, $message, strlen($message));
echo "Message sent to the server: $message\n";

// 从服务器接收响应
$buffer = '';
while ($out = socket_read($socket, 1024)) {
    $buffer .= $out;
    var_dump($buffer);
    if (strpos($buffer,"\r\n")){
        break;
    }
}


echo "Received response from the server: $buffer\n";

// 关闭套接字
socket_close($socket);

