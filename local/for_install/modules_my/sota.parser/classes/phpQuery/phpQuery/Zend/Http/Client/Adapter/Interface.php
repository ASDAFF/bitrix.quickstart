<?php
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

nterface Zend_Http_Client_Adapter_Interface
{
    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array());

    /**
     * Connect to the remote server
     *
     * @param string $host
     * @param int $port
     * @param boolean $secure
     */
    public function connect($host, $port = 80, $secure = false);

    /**
     * Send request to the remote server
     *
     * @param string $method
     * @param Zend_Uri_Http $url
     * @param string $http_ver
     * @param array $headers
     * @param string $body
     * @return string Request as text
     */
    public function write($method, $url, $http_ver = '1.1', $headers = array(), $body = '');

    /**
     * Read response from server
     *
     * @return string
     */
    public function read();

    /**
     * Close the connection to the server
     *
     */
    public function close();
}
