<?php
/**
 *  module
 *
 * @category    
 * @link        http://.ru
 * @revision    $Revision$
 * @date        $Date$
 */

namespace Site\Main;

/**
 * Обслуживает Curl
 */
class Curl
{
    
    /**
     * Возвращает содержимое файла через cURL методом GET
     *
     * @param string $url URL файла
     * @param integer $timeout Таймаут в секундах
     * @return string
     */
    function getCurlGet($url, $timeout = 10)
    {
        if (!function_exists('curl_init')) {
            throw new Exception('cURL module is not installed.');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($ch);

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCode = curl_errno($ch);
        $errString = '';
        if ($errCode) {
            $errString = curl_error($ch);
        }

        curl_close($ch);

        if ($errCode) {
            throw new Exception('cURL error: ' . $errString, $errCode);
        } elseif ($httpStatus >= 400) {
            throw new Exception('Server return error status: ' . $httpStatus);
        }

        return $data;
    }
    
    /**
     * Возвращает содержимое файла через cURL методом POST
     *
     * @param string $url URL файла
     * @param integer $timeout Таймаут в секундах
     * @param array $fields массив полей для запроса
     * @param array $headers массив заголовк для запроса
     * @return string
     */
    function getCurlPost($url, $timeout = 10, $fields = array(), $headers = array())
    {
        if (!function_exists('curl_init')) {
            throw new Exception('cURL module is not installed.');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if(!empty($fields))
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        if(!empty($headers))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCode = curl_errno($ch);
        $errString = '';
        if ($errCode) {
            $errString = curl_error($ch);
        }

        curl_close($ch);

        if ($errCode) {
            throw new Exception('cURL error: ' . $errString, $errCode);
        } elseif ($httpStatus >= 400) {
            throw new Exception('Server return error status: ' . $httpStatus);
        }

        return $data;
    }
    
}