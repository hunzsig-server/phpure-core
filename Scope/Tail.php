<?php

namespace PhpureCore\Scope;

use Closure;
use PhpureCore\Core;
use PhpureCore\Exception\Exception;

class Tail
{

    private static $tail = [];

    /**
     * 添加 tail
     * @param Closure | string $callClass
     */
    public static function add($callClass)
    {
        if (empty($callClass)) Exception::throw('no call class');
        // if call instanceof string, convert it to Closure
        if (is_string($callClass)) {
            if (class_exists($callClass)) {
                $call = function ($request, ...$params) use ($callClass) {
                    $Tail = Core::get($callClass, $request);
                    if (!$Tail instanceof Middleware) {
                        Exception::throw("Class {$callClass} is not instanceof Middleware");
                    }
                    $Tail->handle($params);
                };
            }
        } // if call instanceof Closure, combine the middleware and
        if ($call instanceof Closure) {
            static::$tail[] = $call;
        }
    }

    /**
     * 获取 tail
     * @return array
     */
    public static function fetch()
    {
        $n = static::$tail;
        static::$tail = [];
        return $n;
    }

}