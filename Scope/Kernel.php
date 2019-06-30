<?php

namespace Yonna\Scope;

/**
 * Class Kernel
 * @package Core\Core\Scope
 */
abstract class Kernel implements \Yonna\Scope\Interfaces\Kernel
{

    /**
     * @var \Yonna\IO\Request $request
     */
    private $request = null;


    /**
     * abstractScope constructor.
     * bind the Request
     * @param object $request
     */
    public function __construct(object $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return object|\Yonna\IO\Request
     */
    protected function request()
    {
        return $this->request;
    }

    /**
     * @return \Yonna\IO\Input
     */
    protected function input()
    {
        return $this->request()->input;
    }

}