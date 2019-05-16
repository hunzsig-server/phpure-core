<?php

namespace PhpureCore\Config;

use Closure;
use Exception;

class Scope extends Arrow
{

    const name = 'scope';

    public function __construct()
    {
        if (!isset(self::$stack[self::name])) {
            self::$stack[self::name] = array();
        }
        return $this;
    }
    
    /**
     * 通用添加方法
     * @param string $method
     * @param string $key
     * @param Closure $call
     * @throws \Exception
     */
    public static function add(string $method, string $key, Closure $call)
    {
        if (empty($method)) throw new Exception('must method');
        if (empty($key)) throw new Exception('must key');
        if (empty($call)) throw new Exception('must call');
        // upper
        $method = strtoupper($method);
        $key = strtoupper($key);
        if (!isset(self::$stack[self::name][$method])) {
            self::$stack[self::name][$method] = array();
        }
        if (!isset(self::$stack[self::name][$method][$key])) {
            self::$stack[self::name][$method][$key] = array();
        }
        array_push(self::$stack[self::name][$method][$key], $call);
    }

    /**
     * POST
     * @param string $key
     * @param Closure $call
     */
    public static function post(string $key, Closure $call)
    {
        self::add('post', $key, $call);
    }

    /**
     * GET
     * @param string $key
     * @param Closure $call
     */
    public static function get(string $key, Closure $call)
    {
        self::add('get', $key, $call);
    }

    /**
     * PUT
     * @param string $key
     * @param Closure $call
     */
    public static function put(string $key, Closure $call)
    {
        self::add('put', $key, $call);
    }

    /**
     * DELETE
     * @param string $key
     * @param Closure $call
     */
    public static function delete(string $key, Closure $call)
    {
        self::add('delete', $key, $call);
    }

    /**
     * PATCH
     * @param string $key
     * @param Closure $call
     */
    public static function patch(string $key, Closure $call)
    {
        self::add('patch', $key, $call);
    }

}