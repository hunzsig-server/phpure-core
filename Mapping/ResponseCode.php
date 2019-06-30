<?php
/**
 * Bootstrap Response Code
 */

namespace Yonna\Mapping;

/**
 * Class ResponseCode
 * @package Core\Core\Response
 */
class ResponseCode extends Mapping
{

    const SUCCESS = 200;
    const BROADCAST = 201;
    const GOON = 202;
    const INFO = 300;
    const EXCEPTION = 400;
    const ERROR = 401;
    const NOT_PERMISSION = 403;
    const NOT_FOUND = 404;
    const ABORT = 405;

}