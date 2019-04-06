<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Requests;


class StatusRequest extends BaseRequest {
    /**
     * @var bool
     */
    private $isOtherSystemId;


    /**
     * @param $id
     * @param bool $isOtherSystemId
     */
    function __construct($id, $isOtherSystemId = false)
    {
        parent::__construct($id);
        $this->isOtherSystemId = $isOtherSystemId;
    }


    /**
     * @param bool $isOtherSystemId
     */
    public function setIsOtherSystemId($isOtherSystemId)
    {
        $this->isOtherSystemId = $isOtherSystemId;
    }

    /**
     * @return bool
     */
    public function isOtherSystemId()
    {
        return $this->isOtherSystemId;
    }

} 