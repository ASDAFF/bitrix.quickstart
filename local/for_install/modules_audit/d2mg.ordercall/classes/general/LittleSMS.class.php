<?php

/**
 * Класс для работы с сервисом LittleSMS.ru
 *
 * Функции:
 *  - сообщения: отправка SMS, получение статусов, история
 *  - аккаунт: проверка баланса
 *  - контакты: добавление, обновление, удаление, список
 *  - теги: добавление, обновление, удаление, список
 *  - рассылки: добавление, обновление, удаление, список рассылок, отправка, история
 *  - задания: добавление, обновление, удаление, список
 *
 *  подробнее: wiki.littlesms.ru
 *
 * @author Рустам Миниахметов <pycmam@gmail.com>
 */
class LittleSMS
{
    const REQUEST_SUCCESS = 'success';
    const REQUEST_ERROR = 'error';

    protected
        $user       = null,
        $key        = null,
        $testMode   = false,
        $url        = 'littlesms.ru/api',
        $useSSL     = false,
        $response   = null;

    /**
     * Конструктор
     *
     * @param string $user
     * @param string $key
     * @param integer $testMode
     */
    public function __construct($user, $key, $useSSL = false, $testMode = false)
    {
        $this->user = $user;
        $this->key = $key;
        $this->useSSL = $useSSL;
        $this->testMode = $testMode;
    }

    /**
     * Отправить SMS
     *
     * @param string|array $recipients
     * @param string $message
     * @param string $sender
     * @param boolean $flash
     *
     * @return boolean|integer
     * @deprecated
     */
    public function sendSMS($recipients, $message, $sender = null)
    {
        return $this->messageSend($recipients, $message, $sender);
    }

    /**
     * Проверить статус доставки сообщений
     *
     * @param string|array $messagesId
     *
     * @return boolean|array
     * @deprecated
     */
    public function checkStatus($messagesId)
    {
        return $this->messageStatus($messagesId);
    }

    /**
     * Отправить SMS
     *
     * @param string|array $recipients
     * @param string $message
     * @param string $sender
     * @param boolean $flash
     *
     * @return boolean|integer
     */
    public function messageSend($recipients, $message, $sender = null)
    {
        $params = array(
            'recipients'    => $recipients,
            'message'       => $message,
            'sender'        => $sender,
        );

        if ($this->testMode) {
            $params['test'] = 1;
        }

        $response = $this->makeRequest('message/send', $params);

        return $response['status'] == self::REQUEST_SUCCESS;
    }

    /**
     * Проверить статус доставки сообщений
     *
     * @param string|array $messagesId
     *
     * @return boolean|array
     */
    public function messageStatus($messagesId)
    {
        if (! is_array($messagesId)) {
            $messagesId = array($messagesId);
        }

        $response = $this->makeRequest('message/status', array(
            'messages_id' => join(',', $messagesId),
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['messages'] : false;
    }

    /**
     * Запрос стоимости сообщения
     *
     * @param string|array $recipients
     * @param string $message
     *
     * @return boolean|decimal
     */
    public function messagePrice($recipients, $message)
    {
        $response = $this->makeRequest('message/price', array(
            'recipients'    => $recipients,
            'message'       => $message,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['price'] : false;
    }

    /**
     * История сообщений
     *
     * @param array $params
     *
     * @return boolean|array
     */
    public function messageHistory($params = array())
    {
        $response = $this->makeRequest('message/history', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['history'] : false;
    }

    /**
     * Запросить баланс
     *
     * @return boolean|float
     */
    public function userBalance()
    {
        $response = $this->makeRequest('user/balance');

        return $response['status'] == self::REQUEST_SUCCESS ? (float) $response['balance'] : false;
    }

    /**
     * Запросить баланс
     *
     * @return boolean|float
     * @deprecated
     */
    public function getBalance()
    {
        return $this->userBalance();
    }

    /**
     * Контакты: список контактов
     *
     * @param array $params
     *
     * @return boolean|array
     */
    public function contactList($params = array())
    {
        $response = $this->makeRequest('contact/list', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['contacts'] : false;
    }

    /**
     * Контакты: создать
     *
     * @param array $params
     *
     * @return boolean|integer
     */
    public function contactCreate(array $params) // $phone, $name = null, $description = null, $tags = array())
    {
        $response = $this->makeRequest('contact/create', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['id'] : false;
    }

    /**
     * Контакты: обновить
     *
     * @param integer $id
     * @param array $params
     *
     * @return boolean|integer
     */
    public function contactUpdate($id, array $params)
    {
        $response = $this->makeRequest('contact/update', array_merge($params, array(
            'id' => $id,
        )));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['id'] : false;
    }

    /**
     * Контакты: удалить
     *
     * @param integer $id
     *
     * @return boolean|integer
     */
    public function contactDelete($id)
    {
        $response = $this->makeRequest('contact/delete', array(
            'id' => is_array($id) ? join(',', $id) : $id,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['count'] : false;
    }

    /**
     * Теги: список тегов
     *
     * @param array $params
     *
     * @return boolean|array
     */
    public function tagList($params = array())
    {
        $response = $this->makeRequest('tag/list', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['tags'] : false;
    }

    /**
     * Теги: создать
     *
     * @param array $params
     *
     * @return boolean|integer
     */
    public function tagCreate(array $params)
    {
        $response = $this->makeRequest('tag/create', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['id'] : false;
    }

    /**
     * Теги: обновить
     *
     * @param integer $id
     * @param array $params
     *
     * @return boolean|integer
     */
    public function tagUpdate($id, array $params)
    {
        $response = $this->makeRequest('tag/update', array_merge($params, array(
            'id' => $id,
        )));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['id'] : false;
    }

    /**
     * Теги: удалить
     *
     * @param integer $id
     *
     * @return boolean|integer
     */
    public function tagDelete($id)
    {
        $response = $this->makeRequest('tag/delete', array(
            'id' => is_array($id) ? join(',', $id) : $id,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['count'] : false;
    }

    /**
     * Задания: список заданий
     *
     * @param array $params
     *
     * @return boolean|array
     */
    public function taskList($params = array())
    {
        $response = $this->makeRequest('task/list', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['tasks'] : false;
    }

    /**
     * Задания: создать
     *
     * @param array $params
     *
     * @return boolean|integer
     */
    public function taskCreate(array $params)
    {
        $response = $this->makeRequest('task/create', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['id'] : false;
    }

    /**
     * Задания: обновить
     *
     * @param integer $id
     * @param array $params
     *
     * @return boolean|integer
     */
    public function taskUpdate($id, array $params)
    {
        $response = $this->makeRequest('task/update',  array_merge($params, array(
            'id' => $id,
        )));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['id'] : false;
    }

    /**
     * Задания: удалить
     *
     * @param integer $id
     *
     * @return boolean|integer
     */
    public function taskDelete($id)
    {
        $response = $this->makeRequest('task/delete', array(
            'id' => is_array($id) ? join(',', $id) : $id,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['count'] : false;
    }

    /**
     * Рассылки: список рассылок
     *
     * @param array $params
     *
     * @return boolean|array
     */
    public function bulkList($params = array())
    {
        $response = $this->makeRequest('bulk/list', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['bulks'] : false;
    }

    /**
     * Рассылки: создать
     *
     * @param array $params
     *
     * @return boolean|integer
     */
    public function bulkCreate(array $params)
    {
        $response = $this->makeRequest('bulk/create', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['id'] : false;
    }

    /**
     * Рассылки: обновить
     *
     * @param integer $id
     * @param array $params
     *
     * @return boolean|integer
     */
    public function bulkUpdate($id, array $params)
    {
        $response = $this->makeRequest('bulk/update',  array_merge($params, array(
            'id' => $id,
        )));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['id'] : false;
    }

    /**
     * Рассылки: удалить
     *
     * @param array|integer $id
     * @param array $params
     *
     * @return boolean|integer
     */
    public function bulkDelete($id)
    {
        $response = $this->makeRequest('bulk/delete', array(
            'id' => is_array($id) ? join(',', $id) : $id,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['count'] : false;
    }

    /**
     * Рассылки: отправить
     *
     * @param integer $id
     *
     * @return boolean|integer
     */
    public function bulkSend($id)
    {
        $response = $this->makeRequest('bulk/send', array(
            'id' => $id,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['history_id'] : false;
    }

    /**
     * Рассылки: отменить
     *
     * @param integer $historyId
     *
     * @return boolean|integer
     */
    public function bulkCancel($historyId)
    {
        $response = $this->makeRequest('bulk/cancel', array(
            'hostory_id' => $historyId,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['history_id'] : false;
    }

    /**
     * Рассылки: история
     *
     * @param array $params
     *
     * @return boolean|array
     */
    public function bulkHistory($params = array())
    {
        $response = $this->makeRequest('bulk/history', $params);

        return $response['status'] == self::REQUEST_SUCCESS ? $response['history'] : false;
    }

    /**
     * Отправить запрос
     *
     * @param string $function
     * @param array $params
     *
     * @return stdClass
     */
    protected function makeRequest($function, array $params = array())
    {
        $params = $this->joinArrayValues($params);
        $sign = $this->generateSign($params);
        $params = array_merge(array('user' => $this->user), $params);

        $url = ($this->useSSL ? 'https://' : 'http://') . $this->url .'/'. $function;
        $post = http_build_query(array_merge($params, array('sign' => $sign)), '', '&');

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            if ($this->useSSL) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => $post,
                    'timeout' => 10,
                ),
            ));
            $response = file_get_contents($url, false, $context);
        }

        return $this->response = json_decode($response, true);
    }

    /**
     * Возвращает ответ сервера последнего запроса
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * Установить адрес шлюза
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }


    /**
     * Получить адрес сервера
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    protected function joinArrayValues($params)
    {
        $result = array();
        foreach ($params as $name => $value) {
            $result[$name] = is_array($value) ? join(',', $value) : $value;
        }
        return $result;
    }

    /**
     * Сгенерировать подпись
     *
     * @param array $params
     * @return string
     */
    protected function generateSign(array $params)
    {
        ksort($params);

        return md5(sha1($this->user . join('', $params) . $this->key));
    }
}
