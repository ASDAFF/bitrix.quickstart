<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Sberbank\Exception;

/**
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class BadResponseException extends SberbankAcquiringException
{
    private $response;

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }
}
