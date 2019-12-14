<?php

namespace Lema\Seo;

use Lema\Sberbank\HttpClient\AbstractClient;
use Lema\Sberbank\Exception\BadResponseException;

/**
 * Class VK
 * @package Lema\Seo
 */
class VK extends AbstractClient
{
    const API_URL = 'https://api.vk.com/method/';
    const VERSION = '5.52';

    protected $domain = null;
    protected $accessToken = null;


    /**
     * VK constructor.
     *
     * @param null $domain
     * @param null $accessToken
     *
     * @access public
     */
    public function __construct($accessToken = null, $domain = null)
    {
        if(!empty($domain))
            $this->setDomain($domain);
        if(!empty($accessToken))
            $this->setAccessToken($accessToken);
    }

    /**
     * Returns current HTTP Method
     *
     * @return string
     *
     * @access public
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Set HTTP method (GET/POST/PATCH/etc..)
     *
     * @param string $httpMethod
     *
     * @return $this
     *
     * @access public
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }

    /**
     * Returns domain name
     *
     * @return null|string
     *
     * @access public
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param null $domain
     *
     * @return $this
     *
     * @access public
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * Returns VK API access_token
     *
     * @return null|string
     *
     * @access public
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set VK API access_token
     *
     * @param null $accessToken
     *
     * @return $this
     *
     * @access public
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }


    /**
     * Returns array of posts on wall
     *
     * @return array
     *
     * @access public
     */
    public function getWall()
    {
        return $this->execute('wall.get', array(
            'domain' => $this->getDomain(),
        ));
    }

    /**
     * Execute an action.
     *
     * @param string $action An action's name e.g. 'register.do'
     * @param array  $data   An actions's data
     *
     * @throws NetworkException
     *
     * @return array A server's response
     *
     * @access public
     */
    public function execute($action, array $data = array())
    {
        $uri = static::API_URL . $action;

        $headers = array(
            'Cache-Control: no-cache',
        );

        $data['v'] = static::VERSION;
        $data['access_token'] = $this->getAccessToken();

        $httpClient = $this->getHttpClient();

        list($httpCode, $response) = $httpClient->request($uri, $this->getHttpMethod(), $headers, $data);

        if (200 !== $httpCode) {
            $badResponseException = new BadResponseException(sprintf('Bad HTTP code: %d.', $httpCode), $httpCode);
            $badResponseException->setResponse($response);

            throw $badResponseException;
        }

        $response = $this->parseResponse($response);
        $this->handleErrors($response);

        return $response;
    }

}