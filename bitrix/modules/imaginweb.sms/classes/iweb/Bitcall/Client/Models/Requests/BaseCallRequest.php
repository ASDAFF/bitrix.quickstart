<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 16.05.2014
 */


namespace Bitcall\Client\Models\Requests;


class BaseCallRequest extends BaseRequest {
    private $phone;
    private $callerPhone;

    function __construct($callerPhone, $phone, $id = null)
    {
        parent::__construct($id);
        $this->callerPhone = $callerPhone;
        $this->phone = $phone;
    }

    /**
     * @param string $callerPhone
     */
    public function setCallerPhone($callerPhone)
    {
        $this->callerPhone = $callerPhone;
    }

    /**
     * @return string
     */
    public function getCallerPhone()
    {
        return $this->callerPhone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

} 