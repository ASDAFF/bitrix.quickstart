<?php

namespace Lm\Sberbank\HttpClient;

use \Lm\Sberbank\Exception\NetworkException;

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
