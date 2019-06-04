<?php

namespace PhpureCore\Event;

use PhpureCore\Core;

/**
 * Class Middleware
 * @package PhpureCore\Scope
 */
abstract class Event
{

    private $params = null;
    private $listeners = [];

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * 获取参数
     * @return null
     */
    public function getParams(){
        return $this->params;
    }

    /**
     * 设定listeners
     * @param array $listeners
     */
    public function listener(array $listeners)
    {
        $this->listeners = $listeners;
        foreach ($this->listeners as $l) {
            Core::get($l, $this)->handle();
        }
    }

}