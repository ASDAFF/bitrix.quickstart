<?php

IncludeModuleLangFile(__FILE__);

class UniAPI
{
    const DOMAIN = 'https://api.unisender.com/';

    protected $API_KEY;
    protected $Encoding = 'CP1251';
    protected $error = array();

    public function __construct($API_KEY)
    {
        $this->API_KEY = $API_KEY;
        if (defined('BX_UTF')) {
            $this->Encoding = 'UTF-8';
        }
    }

    public function UniConvertCharset(&$value, $key)
    {
        global $APPLICATION;
        $value = (string)$APPLICATION->ConvertCharset($value, $this->Encoding, 'UTF-8');
    }

    public function UniConvertResponse(&$value, $key)
    {
        global $APPLICATION;
        $value = (string)$APPLICATION->ConvertCharset($value, 'UTF-8', $this->Encoding);
    }

    public function showError()
    {
        $error = $this->getError();
        echo '<span class="errortext">API ERROR: ' . $error[0]
            . ' (code: ' . $error[1] . ')</span>';
    }

    public function getError()
    {
        return $this->error;
    }

    public function getLists()
    {
        if (!$data = $this->exec('getLists')) {
            return false;
        }

        $lists = array();
        foreach ($data->result as $list) {
            $lists[] = array(
                'id' => (int)$list->id,
                'title' => $this->getConverted(trim($list->title))
            );
        }

        return $lists;
    }

    private function query($method, array $params = array())
    {
        $POST = array(
            'api_key' => $this->API_KEY,
            'platform' => 'Bitrix 2.0',
            'format' => 'json'
        );

        if ($this->Encoding !== 'UTF-8') {
            array_walk_recursive($params, array($this, 'UniConvertCharset'));
        }

        $POST = array_merge($POST, $params);

        $ContextOptions = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($POST)
            )
        );

        $Context = stream_context_create($ContextOptions);
        $result = file_get_contents(
            self::DOMAIN . LANGUAGE_ID . '/api/' . $method,
            FALSE, $Context
        );

        try {
            $result = json_decode($result);
            if (null === $result) {
                $this->error = array(
                    $this->getConverted(GetMessage('UNI_ERROR_SERVER_JSON')),
                    500
                );

                return false;
            }
            if (!$this->isSuccess($result)) {
                return false;
            }
        } catch (Exception $e) {
            $this->error = array(
                $this->getConverted(GetMessage('UNI_ERROR_SERVER_JSON')),
                500
            );
            return false;
        }

        return $result;
    }

    private function getConverted($value)
    {
        return $this->Encoding !== 'UTF-8'
            ? iconv('UTF-8', $this->Encoding, $value)
            : $value;
    }

    private function isSuccess($jsonResult)
    {
        //TODO тут не все варианты учитываются, вроде как
        if (!$jsonResult->error) {
            return true;
        }

        $this->error = array(
            $this->getConverted($jsonResult->error),
            $this->getConverted($jsonResult->code)
        );

        return false;
    }

    public function getFields()
    {
        if (!$data = $this->exec('getFields')) {
            return false;
        }

        $fields = array();
        foreach ($data->result as $field) {
            $fields[$field->name] = array(
                'id' => $field->id,
                'name' => $this->getConverted(trim($field->name)),
                'public_name' => $this->getConverted(trim($field->public_name)),
                'type' => $field->type,
                'is_visible' => $field->is_visible,
                'view_pos' => $field->view_pos
            );
        }

        return $fields;
    }

    public function importContacts($params)
    {
        if (!$data = $this->exec('importContacts', $params)) {
            return false;
        }

        if (!empty($data->result->log)) {
            $logs = array();
            foreach ($data->result->log as $log) {
                $logs[] = $this->getConverted($log->message);
            }
        }

        return array(
            'total' => (int)$data->result->total,
            'inserted' => (int)$data->result->inserted,
            'updated' => (int)$data->result->updated,
            'deleted' => (int)$data->result->deleted,
            'new_emails' => (int)$data->result->new_emails,
            'logs' => isset($logs) ? $logs : null
        );
    }

    public function exportContacts($params)
    {
        return $this->exec('exportContacts', $params);
    }

    public function registerAccount($params)
    {
        $method = 'register';
        $params = array(
            'email' => $params['email'],
            'login' => $params['login'],
            'password' => $params['password'],
            'notify' => 1,
            'api_mode' => 'on',
            'need_confirm' => 1,
            'extra[firstname]' => $params['firstname'],
            'extra[lastname]' => $params['lastname']
        );

        $newAccount = $this->exec($method, $params);

        return $newAccount;
    }

    public function subscribe($params)
    {
        return $this->exec('subscribe', $params);
    }

    public function createList($title)
    {
        return $this->exec('createList', array('title' => $title));
    }

    public function createField($params)
    {
        $params = array(
            'name' => $params['name'],
            'public_name' => $params['public_name'],
            'type' => $params['type']
        );
        return $this->exec('createField', $params);
    }

    public function exec($method, array $params = array())
    {
        $url = self::DOMAIN . LANGUAGE_ID . '/api/' . $method;

        $params = array_merge(
            array(
                'api_key' => $this->API_KEY,
                'format' => 'json',
                'platform' => 'Bitrix 2.0'
            ),
            $params
        );

        if ($this->Encoding !== 'UTF-8') {
            array_walk_recursive($params, array($this, 'UniConvertCharset'));
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_USERAGENT, 'HAC');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        $result = curl_exec($curl);
        curl_close($curl);

        try {
            $result = json_decode($result);
            if (null === $result) {
                $this->error = array(
                    $this->getConverted(GetMessage('UNI_ERROR_SERVER_JSON')),
                    500
                );
                return false;
            }
            if (!$this->isSuccess($result)) {
                return false;
            }
        } catch (Exception $e) {
            $this->error = array(
                $this->getConverted(GetMessage('UNI_ERROR_SERVER_JSON')),
                500
            );
            return false;
        }

        if ($this->Encoding !== 'UTF-8') {
            array_walk_recursive($result->result->data, array($this, 'UniConvertResponse'));
        }

        return $result;
    }

}
