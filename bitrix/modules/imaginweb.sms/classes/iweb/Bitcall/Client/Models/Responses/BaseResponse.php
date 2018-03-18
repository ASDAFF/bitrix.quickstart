<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Responses;


abstract class BaseResponse {
    private $error;
    private $hasError;

    function __construct($error, $hasError)
    {
        $this->error = $error;
        $this->hasError = $hasError;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function hasError()
    {
        return $this->hasError;
    }


} 