<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Responses;


use Bitcall\Client\Models\Common\ResponseError;

class BaseCallResponse extends BaseResponse {
    private $id;


    /**
     * @param string $id
     * @param ResponseError $error
     * @param bool $hasError
     */
    function __construct($id, $error, $hasError)
    {
        parent::__construct($error, $hasError);
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