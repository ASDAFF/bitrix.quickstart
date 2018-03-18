<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Responses;


class BaseTaskResponse extends  BaseResponse {
    private $id;

    function __construct($id, $error, $hasError)
    {
        parent::__construct($error, $hasError);
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}