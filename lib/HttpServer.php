<?php

namespace fish\lib;

class HttpServer {
    
    protected $http = null;
    public $server;
    public $get;
    public $post;
    public $header;
    public $rootPath = null;
    public $argv = [];
    public $options
        = [
            'worker_num',
            'daemonize',
            "log_file"
        ];
    
    public function __construct($port = 9503) {
        echo "###HttpServer start on $port####\n";
        $this->http = new \swoole_http_server("127.0.0.1", $port);
        $this->http->set(
            array(
                'worker_num' => 10,
                'daemonize' => false,
                'max_request' => 10000,
                'dispatch_mode' => 1,
                'user' => 'nobody',
                'group' => 'nobody'
            )
        );
        $this->http->on('WorkerStart', array($this, "onWorkerStart"));
        $this->http->on('request', array($this, "onRequest"));
    }
    
    
    public function setRoot($rootPath) {
        $this->rootPath = rtrim($rootPath, '/');
    }
    
    public function setArgv(array $argv) {
        $this->argv = $argv;
        $cmd1       = isset($argv[1]) ? $argv[1] : "";
        $cmd2       = isset($argv[2]) ? $argv[2] : "";
        $startFile   = $argv[0]??"";
        $cmd        = $cmd1 . $cmd2;
        switch ($cmd) {
            case "start":
                $this->http->set(["daemonize" => false]);
                break;
            case "start-d":
                $this->http->set(["daemonize" => true]);
                break;
            case "stop":
                //todo
                exec("ps aux | grep $startFile | grep -v grep | awk '{print $2}' |xargs kill -SIGTERM");
                exit();
                break;
            default:
                echo "Usage(php  yourfile start|start -d|restart -d|stop)" . PHP_EOL;
                exit();
            //@posix_kill($master_pid, 0)
        }
        return 1;
    }
    
    public function setOptions(array $options = []) {
        foreach ($options ?: [] as $k => $v) {
            if (!in_array($k, $this->options)) {
                unset($options[$k]);
            }
        }
        $this->http->set($options);
    }
    
    public function run() {
        $this->http->start();
    }
    
    public function onWorkerStart($server, $worker_id) {
        echo 'worker start' . PHP_EOL;
    }
    
    public function onRequest($request, $response) {
        if (isset($request->server)) {
            $this->server = $request->server;
        } else {
            $this->server = [];
        }
        if (isset($request->header)) {
            $this->header = $request->header;
        } else {
            $this->header = [];
        }
        if (isset($request->get)) {
            $this->get = $request->get;
        } else {
            $this->get = [];
        }
        if (isset($request->post)) {
            $this->post = $request->post;
        } else {
            $this->post = [];
        }
        
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            return $response->end();
        }
        $this->handle($request, $response);
    }
    
    public function handle($request, $response) {
        $request_uri = $request->server['request_uri'];
        $request_uri = $request_uri != "/" ? $request_uri : "/index/index";
        $file        = $this->rootPath . $request_uri;
        $_file       = "$file.php";
        if (is_file($_file)) {
            ob_start();
            try {
                include $_file;
                $body = ob_get_contents();
            } catch (\Exception $e) {
                $response->status(500);
                $body = $e->getMessage();
            }
            ob_end_clean();
        } else {
            $response->status(404);
            $body = "$request_uri not found";
        }
        $response->end($body);
    }
    
    
}

