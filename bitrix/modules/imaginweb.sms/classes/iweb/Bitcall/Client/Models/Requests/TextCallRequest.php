<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 16.05.2014
 */


namespace Bitcall\Client\Models\Requests;


class TextCallRequest extends  BaseCallRequest {
    private $text;

    function __construct($text = null, $callerPhone = null, $phone = null, $id = null)
    {
        parent::__construct($callerPhone, $phone, $id);
        $this->text = $text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }
}