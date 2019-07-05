<?php

namespace Yonna\Middleware;

use Closure;
use Yonna\Core;
use Yonna\Exception\Exception;
use Yonna\IO\Request;

class Before extends Middleware
{

    private static $before = [];

    /**
     * @var Request
     */
    private $request = null;

    /**
     * After constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * get middleware
     * @return string
     */
    public static function type(): string
    {
        return MiddlewareType::BEFORE;
    }

    /**
     * 添加 before
     * @param Closure | string $call
     */
    public static function add($call)
    {
        if (empty($call)) Exception::throw('no call class');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                $call = function ($request) use ($call) {
                    $Before = Core::get($call, $request);
                    if (!$Before instanceof Before) {
                        Exception::throw("Class {$call} is not instanceof Middleware-Before");
                    }
                    $Before->handle();
                };
            }
        } // if call instanceof Closure, combine the middleware and
        if ($call instanceof Closure) {
            static::$before[] = $call;
        }
    }

    /**
     * 获取 before
     * @return array
     */
    public static function fetch()
    {
        return static::$before;
    }

    /**
     * 清空before
     */
    public static function clear()
    {
        static::$before = [];
    }

}