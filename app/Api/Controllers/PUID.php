<?php namespace Api\Controllers;

use JWTAuth;
use DB;
use Validator;


class PUID {

    /**
     * @var
     */
    public $prefix;

    /**
     * @var
     */
    public $entropy;

    /**
     * @param string $prefix
     * @param bool $entropy
     */
    public function __construct($prefix = 'CJ_', $entropy = false)
    {
        $this->puid = uniqid($prefix, $entropy);
    }

    /**
     * Limit the PUID by a number of characters
     *
     * @param $length
     * @param int $start
     * @return $this
     */
    public function limit($length, $start = 5)
    {
        $this->puid = substr($this->puid, $start, $length);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->puid;
    }
}