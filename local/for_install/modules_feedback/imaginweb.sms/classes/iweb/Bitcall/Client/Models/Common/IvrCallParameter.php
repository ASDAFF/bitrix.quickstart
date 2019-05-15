<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Common;


class IvrCallParameter {
    private $ivrOperator;
    private $parameters;


    function __construct($ivrOperator, array $parameters = null)
    {
        $this->ivrOperator = $ivrOperator;
        $this->parameters = $parameters;
    }


    /**
     * @param string $ivrOperator
     */
    public function setIvrOperator($ivrOperator)
    {
        $this->ivrOperator = $ivrOperator;
    }

    /**
     * @return string
     */
    public function getIvrOperator()
    {
        return $this->ivrOperator;
    }

    /**
     * @param array
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    public function appendParameter($parameter)
    {
        if($this->parameters === null){
            $this->parameters = array();
        }
        $this->parameters[] = $parameter;
    }
}