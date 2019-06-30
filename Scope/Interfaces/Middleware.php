<?php

namespace Yonna\Scope\Interfaces;

/**
 * Interface Middleware
 * @package Core\Core\Interfaces
 */
interface Middleware
{

    public static function type(): string;

    public function handle($params);

}

