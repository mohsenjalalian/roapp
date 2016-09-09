<?php

namespace AppBundle\Utils;


class TokenGenerator
{
    /**
     * @var string
     */
    private $key;

    /**
     * TokenGenerator constructor.
     * 
     */
    public function __construct()
    {
    }

    /**
     * Generate token based on key.
     *
     * @param string $key
     */
    public function generate($key)
    {
        return uniqid(md5($key));
    }
}