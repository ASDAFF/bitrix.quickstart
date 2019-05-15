<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Requests;


use Bitcall\Client\Models\Common\HasIvrCallParameters;
use Bitcall\Client\Models\Common\IvrCallParameter;

class IvrTaskCall extends TaskCall {

    /**
     * @param $phone
     * @param null IvrCallParameter[] $context
     * @param null $id
     */
    function __construct($phone, $context = null, $id = null)
    {
        parent::__construct($phone, $id);
        $this->saveContext($context);
    }

    private $context;

    /**
     * @param IvrCallParameter[] $context
     */
    protected function saveContext($context){
        if($context === null){
            $this->context = array();
        } else {
            $this->context = $context;
        }
    }

    public function  appendParameter(IvrCallParameter $parameter)
    {
        $this->context[] = $parameter;
    }

    /**
     * @param IvrCallParameter[] $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return IvrCallParameter[]
     */
    public function getContext()
    {
        return $this->context;
    }
}