<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Common;


class ResponseError {
    private $errorCode;
    private $errorString;
    private $errorMessage;
    private $errorId;

    function __construct($errorCode, $errorId, $errorMessage, $errorString)
    {
        $this->errorCode = $errorCode;
        $this->errorId = $errorId;
        $this->errorMessage = $errorMessage;
        $this->errorString = $errorString;
    }


    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

       /**
     * @return mixed
     */
    public function getErrorId()
    {
        return $this->errorId;
    }

     /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return mixed
     */
    public function getErrorString()
    {
        return $this->errorString;
    }

} 