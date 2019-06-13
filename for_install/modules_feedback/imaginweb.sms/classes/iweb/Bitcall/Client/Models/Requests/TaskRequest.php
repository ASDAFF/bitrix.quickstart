<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Models\Requests;


abstract class TaskRequest {
    private $taskName;
    private $callerPhone;
    private $sendDate;
    private $maxRetries;
    private $retryTime;
    private $waitTime;

    function __construct($callerPhone, $maxRetries = 0, $retryTime = 5, $sendDate = null, $taskName = null, $waitTime = 45)
    {
        $this->callerPhone = $callerPhone;
        $this->maxRetries = $maxRetries;
        $this->retryTime = $retryTime;
        $this->sendDate = $sendDate;
        $this->taskName = $taskName;
        $this->waitTime = $waitTime;
    }


    /**
     * @param mixed $callerPhone
     */
    public function setCallerPhone($callerPhone)
    {
        $this->callerPhone = $callerPhone;
    }

    /**
     * @return mixed
     */
    public function getCallerPhone()
    {
        return $this->callerPhone;
    }

    /**
     * @param mixed $maxRetries
     */
    public function setMaxRetries($maxRetries)
    {
        $this->maxRetries = $maxRetries;
    }

    /**
     * @return mixed
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * @param mixed $retryTime
     */
    public function setRetryTime($retryTime)
    {
        $this->retryTime = $retryTime;
    }

    /**
     * @return mixed
     */
    public function getRetryTime()
    {
        return $this->retryTime;
    }

    /**
     * @param mixed $sendDate
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;
    }

    /**
     * @return mixed
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * @param mixed $taskName
     */
    public function setTaskName($taskName)
    {
        $this->taskName = $taskName;
    }

    /**
     * @return mixed
     */
    public function getTaskName()
    {
        return $this->taskName;
    }

    /**
     * @param mixed $waitTime
     */
    public function setWaitTime($waitTime)
    {
        $this->waitTime = $waitTime;
    }

    /**
     * @return mixed
     */
    public function getWaitTime()
    {
        return $this->waitTime;
    }

} 