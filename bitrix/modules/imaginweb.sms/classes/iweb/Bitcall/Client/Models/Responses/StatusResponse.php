<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Responses;


class StatusResponse extends BaseCallResponse {

    private $status;
    private $digits;
    private $duration;
    private $price;
    private $statusString;

    function __construct($digits, $duration, $price, $status, $statusString, $id, $error, $hasError)
    {
        parent::__construct($id, $error, $hasError);
        $this->digits = $digits;
        $this->duration = $duration;
        $this->price = $price;
        $this->status = $status;
        $this->statusString = $statusString;
    }

    /**
     * @return mixed
     */
    public function getDigits()
    {
        return $this->digits;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getStatusString()
    {
        return $this->statusString;
    }


}