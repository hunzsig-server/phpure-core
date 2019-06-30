<?php

namespace Yonna\Console;

use Exception;
use swoole_server;

/**
 * Class Main
 * @package Core\Core\Console
 */
class SwooleTcp extends Console
{

    private $server = null;
    private $root_path = null;
    private $options = null;

    /**
     * SwooleHttp constructor.
     * @param $root_path
     * @param $options
     * @throws Exception
     */
    public function __construct($root_path, $options)
    {
        if (!class_exists('swoole_server')) {
            throw new Exception('class swoole_server not exists');
        }
        $this->root_path = $root_path;
        $this->options = $options;
        $this->checkParams($this->options, ['p', 'e']);
        return $this;
    }

    public function run()
    {
        $this->server = new swoole_server("0.0.0.0", $this->options['p']);

        $this->server->set(array(
            'worker_num' => 4,
            'task_worker_num' => 10,
            'heartbeat_check_interval' => 10,
            'heartbeat_idle_time' => 180,
        ));

        $this->server->on("start", function () {
            echo "server start" . PHP_EOL;
        });

        $this->server->on("workerStart", function ($worker) {
            echo "worker start" . PHP_EOL;
        });

        $this->server->on('connect', function ($server, $fd) {
            echo "connection open: {$fd}\n";
        });
        $this->server->on('receive', function ($server, $fd, $reactor_id, $data) {
            $request = $data;
            /**
             * 处理数据
             */
            $this->server->task($request, -1, function ($server, $task_id, $result) use ($fd) {
                if ($result !== false) {
                    $server->send($fd, $result);
                    return;
                }
            });
        });
        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $data = $this->io($request);
            $this->server->finish($data);
        });

        $this->server->on('finish', function ($server, $data) {
            echo "AsyncTask Finish" . PHP_EOL;
        });

        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->start();
    }
}
