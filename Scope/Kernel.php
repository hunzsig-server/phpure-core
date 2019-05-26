<?php

namespace PhpureCore\Scope;

/**
 * Class Kernel
 * @package PhpureCore\Scope
 */
abstract class Kernel implements \PhpureCore\Scope\Interfaces\Kernel
{

    /**
     * @var \PhpureCore\IO\Request $request
     */
    private $request = null;

    /**
     * @var \PhpureCore\Database\Coupling $db
     */
    private $db = null;

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
     * @return object|\PhpureCore\IO\Request
     */
    protected function request()
    {
        return $this->request;
    }

    /**
     * @return \PhpureCore\IO\Input
     */
    protected function input()
    {
        return $this->request()->input;
    }

}