<?php

namespace Yonna\Console;

use Exception;
use Workerman\Connection\UdpConnection;
use Yonna\Core;
use Yonna\Bootstrap\BootType;
use Yonna\Response\Collector;

/**
 * Class WorkermanUdp
 * @package Yonna\Console
 */
class WorkermanUdp extends Console
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
        $this->worker = new \Workerman\Worker("udp://0.0.0.0:{$this->options['p']}");

        $this->worker->count = 4;

        $this->worker->onMessage = function (UdpConnection $connection, $message) {
            $responseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::WORKERMAN_UDP,
                array(
                    'connection' => $connection,
                    'request' => $message,
                )
            );
            if ($responseCollector instanceof Collector) {
                $connection->send($responseCollector->response());
            } else {
                $connection->send('response error');
            }
        };

        \Workerman\Worker::runAll();
    }
}