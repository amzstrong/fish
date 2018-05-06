<?php
$http = new swoole_http_server("127.0.0.1", 9502);
$http->set([
    'worker_num' => 4,
]);

$http->on("start", function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:9502\n";
});

$http->on("request", function ($request, $response) {
    $root = __DIR__ . "/www";
    echo $root.PHP_EOL;
    $path = $root . $request->server['request_uri'];//a/b=>  $root/a/b
    $file = $path . ".php";
    echo $file . PHP_EOL;
    
    if (is_file($file)) {
        $response->head['Content-Type'] = 'text/plain';
        ob_start();
        try {
            include $file;
            $body = ob_get_contents();
        } catch (\Exception $e) {
            $response->status(500);
            $body = $e->getMessage();
        }
        ob_end_clean();
    } else {
        $response->status(500);
        $body = "action not found";
    }
    
    $response->end($body);
});

$http->start();


