<?php

namespace AppBundle\Utils;

/**
 * Class TokenGenerator
 * @package AppBundle\Utils
 */
class TokenGenerator
{
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
     * @return int
     */
    public function generate($key)
    {
        return uniqid(md5($key));
    }
}
