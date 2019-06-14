<?php

namespace Admitad\Api;

use Admitad\Api\Exception\ApiException;
use Admitad\Api\Exception\Exception;
use Admitad\Api\Exception\InvalidSignedRequestException;
use Buzz\Client\ClientInterface;
use Buzz\Client\Curl;

class Api
{
    protected $accessToken;
    protected $host = 'https://api.admitad.com';
    private $lastRequest;
    private $lastResponse;

    public function __construct($accessToken = null)
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function authorizeByPassword($clientId, $clientSecret, $scope, $username, $password)
    {
        $query = array(
            'client_id' => $clientId,
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
            'scope' => $scope
        );

        $request = new Request(Request::METHOD_POST, '/token/');
        $request->setContent(http_build_query($query));
        $request->addHeader('Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret));

        return $this->send($request, null, false);
    }

    public function getAuthorizeUrl($clientId, $redirectUri, $scope, $responseType = 'code')
    {
        return $this->host . '/authorize/?' . http_build_query(array(
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'response_type' => $responseType
        ));
    }

    public function parseSignedRequest($signedRequest, $clientSecret)
    {
        if (!$signedRequest || false === strpos($signedRequest, '.')) {
            throw new InvalidSignedRequestException("Invalid signed request " . $signedRequest);
        }

        list ($key, $data) = explode('.', $signedRequest);

        $hash = hash_hmac('sha256', $data, $clientSecret);
        if ($hash != $key) {
            throw new InvalidSignedRequestException("Invalid signed request " . $signedRequest);
        }
        return json_decode(base64_decode($data), true);
    }

    public function requestAccessToken($clientId, $clientSecret, $code, $redirectUri)
    {
        $query = array(
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri
        );

        $request = new Request(Request::METHOD_POST, '/token/');
        $request->setContent(http_build_query($query));

        return $this->send($request, null, false);
    }

    public function refreshToken($clientId, $clientSecret, $refreshToken)
    {
        $query = array(
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token'
        );

        $request = new Request(Request::METHOD_POST, '/token/');
        $request->setContent(http_build_query($query));

        return $this->send($request, null, false);
    }

    public function send(Request $request, Response $response = null, $useAuth = true)
    {
        if (is_null($response)) {
            $response = new Response();
        }

        if (null === $request->getHost()) {
            $request->setHost($this->host);
        }

        $this->lastRequest = $request;
        $this->lastResponse = $response;
        
        if ($useAuth) {
            if (null === $this->accessToken) {
                throw new Exception("Access token not provided");
            }
            $request->addHeader('Authorization: Bearer ' . $this->accessToken);
        }

        $client = $this->createClient();
        $client->send($request, $response);

        if (!$response->isSuccessful()) {
            throw new ApiException('Operation failed: ' . $response->getError(), $request, $response);
        }

        return $response;
    }

    public function get($method, $params = array())
    {
        $resource = $method . '?' . http_build_query($params);
        $request = new Request(Request::METHOD_GET, $resource);
        return $this->send($request);
    }

    public function getIterator($method, $params = array(), $limit = 200)
    {
        return new Iterator($this, $method, $params, $limit);
    }

    public function post($method, $params = array())
    {
        $request = new Request(Request::METHOD_POST, $method);
        $request->setContent(http_build_query($params));
        return $this->send($request);
    }

    public function me()
    {
        return $this->get('/me/');
    }

    public function authorizeClient($clientId, $clientSecret, $scope)
    {
        $query = array(
            'client_id' => $clientId,
            'scope' => $scope,
            'grant_type' => 'client_credentials'
        );

        $request = new Request(Request::METHOD_POST, '/token/');
        $request->addHeader('Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret));
        $request->setContent(http_build_query($query));
        return $this->send($request, null, false);
    }

    public function selfAuthorize($clientId, $clientSecret, $scope)
    {
        $r = $this->authorizeClient($clientId, $clientSecret, $scope);
        $accessToken = $r->getResult('access_token');
        $this->setAccessToken($accessToken);
        return $this;
    }

    /**
     * @return ClientInterface
     */
    protected function createClient()
    {
        $curl = new Curl();
        $curl->setTimeout(300);
        return $curl;
    }
    
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    public function getLastResponse()
    {
        return $this->lastResponse;
    }
}
