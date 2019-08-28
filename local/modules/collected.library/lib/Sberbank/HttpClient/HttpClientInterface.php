<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Sberbank\HttpClient;

use \Collected\Sberbank\Exception\NetworkException;

/**
 * Simple HTTP client interface.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
interface HttpClientInterface
{
    /**
     * Send an HTTP request.
     *
     * @param string $uri
     * @param string $method
     * @param array  $headers
     * @param array  $data
     *
     * @throws NetworkException
     *
     * @return array A response
     */
    public function request($uri, $method = 'GET', array $headers = array(), array $data = array());
}
