<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Responses;


use Bitcall\Client\Models\Common\ResponseError;

class TaskResponse extends BaseTaskResponse {
    private $callResponses;


    /**
     * @param BaseCallResponse[] $callResponses
     * @param $id
     * @param ResponseError $error
     * @param $hasError
     */
    function __construct($callResponses, $id, $error, $hasError)
    {
        parent::__construct($id, $error, $hasError);
        $this->callResponses = $callResponses;
    }

    /**
     * @return BaseCallResponse[]
     */
    public function getCallResponses()
    {
        return $this->callResponses;
    }
}