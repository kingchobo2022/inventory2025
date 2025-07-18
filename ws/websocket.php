<?php
// websocket-server.php
$host = '0.0.0.0';
$port = 8080;
$server = stream_socket_server("tcp://$host:$port", $errno, $errstr);
if (!$server) die("$errstr ($errno)");

echo "WebSocket Server started at $host:$port\n";

while ($conn = stream_socket_accept($server)) {
    $request = fread($conn, 5000);

    // WebSocket 핸드셰이크 처리
    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $request, $matches)) {
        $key = base64_encode(sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $headers = "HTTP/1.1 101 Switching Protocols\r\n";
        $headers .= "Upgrade: websocket\r\n";
        $headers .= "Connection: Upgrade\r\n";
        $headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
        fwrite($conn, $headers);
        echo "Client connected\n";   

        // 간단한 메시지 처리 (한 번만 읽음)
        $data = fread($conn, 1000);
        $unmasked = substr($data, 6); // 단순화 처리
        echo "Received: $unmasked\n";

        $response = "Hello from PHP WebSocket Server";
        $msg = chr(129) . chr(strlen($response)) . $response;
        fwrite($conn, $msg);
    }

    fclose($conn);
}
