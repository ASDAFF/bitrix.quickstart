<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 16.05.14
  */

namespace Bitcall\Client\Models\Requests;


class BaseRequest {
    private $id;

    function __construct($id)
    {
        $this->id = $id;
    }


    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}