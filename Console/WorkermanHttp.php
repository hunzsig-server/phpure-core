<?php

namespace Yonna\Console;

use Exception;
use Yonna\Core;
use Yonna\Bootstrap\BootType;
use Yonna\Response\Collector;

/**
 * Class WorkermanHttp
 * @package Yonna\Console
 */
class WorkermanHttp extends Console
{

    private $worker = null;
    private $root_path = null;
    private $options = null;

    /**
     * \Workerman\Worker constructor.
     * @param $root_path
     * @param $options
     * @throws Exception
     */
    public function __construct($root_path, $options)
    {
        if (!class_exists('\Workerman\Worker')) {
            throw new Exception('class  Workerman\Worker not exists');
        }
        $this->root_path = $root_path;
        $this->options = $options;
        $this->checkParams($this->options, ['p', 'e']);
        return $this;
    }

    public function run()
    {
        $this->worker = new \Workerman\Worker("http://0.0.0.0:{$this->options['p']}");

        $this->worker->count = 4;

        $this->worker->onMessage = function ($connection, $request) {
            $responseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::WORKERMAN_HTTP,
                array(
                    'connection_id' => $connection->id,
                    'worker_id' => $connection->worker->id,
                    'request' => $request,
                )
            );
            if ($responseCollector instanceof Collector) {
                $connection->headers = $responseCollector->getHeader('arr');
                $connection->send($responseCollector->response());
            } else {
                $connection->send('response error');
            }
        };

        \Workerman\Worker::runAll();
    }
}