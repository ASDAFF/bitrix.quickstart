<?php 

/**
 * Class Tinkoff
 */
class Tinkoff
{
    /**
     * After calling initPayment()
     */
    const STATUS_NEW = 'NEW';

    /**
     * After calling cancelPayment()
     * Not Implemented here
     */
    const STATUS_CANCELED = 'CANCELED';

    /**
     * Intermediate status (transaction is in process)
     */
    const STATUS_PREAUTHORIZING = 'PREAUTHORIZING';

    /**
     * After showing payment form to the customer
     */
    const STATUS_FORMSHOWED = 'FORMSHOWED';

    /**
     * Intermediate status (transaction is in process)
     */
    const STATUS_AUTHORIZING = 'AUTHORIZING';

    /**
     * Intermediate status (transaction is in process)
     * Customer went to 3DS
     */
    const STATUS_THREEDSCHECKING = 'THREEDSCHECKING';

    /**
     * Payment rejected on 3DS
     */
    const STATUS_REJECTED = 'REJECTED';

    /**
     * Payment compete, money holded
     */
    const STATUS_AUTHORIZED = 'AUTHORIZED';

    /**
     * After calling reversePayment
     * Charge money back to customer
     * Not Implemented here
     */
    const STATUS_REVERSING = 'REVERSING';

    /**
     * Money charged back, transaction cmplete
     */
    const STATUS_REVERSED = 'REVERSED';

    /**
     * After calling confirmePayment()
     * Confirm money wright-off
     * Not Implemented here
     */
    const STATUS_CONFIRMING = 'CONFIRMING';

    /**
     * Money written off
     */
    const STATUS_CONFIRMED = 'CONFIRMED';

    /**
     * After calling refundPayment()
     * Retrive money back to customer
     * Not Implemented here
     */
    const STATUS_REFUNDING = 'REFUNDING';

    /**
     * Money is back on the customer account
     */
    const STATUS_REFUNDED = 'REFUNDED';

    const STATUS_UNKNOWN = 'UNKNOWN';

    /**
     * Terminal id, bank give it to you
     * @var int
     */
    private $terminalId;

    /**
     * Secret key, bank give it to you
     * @var string
     */
    private $secret;

    /**
     * Read API documentation
     * @var string
     */
    private $paymentUrl;

    /**
     * Current payment status
     * @var string
     */
    private $paymentStatus;

    /**
     * Payment id in bank system
     * @var int
     */
    private $paymentId;

    /**
     * @param $terminalId int
     * @param $secret string
     * @param $paymentUrl string
     */
    public function __construct($terminalId, $secret, $paymentUrl){
        $this->terminalId = $terminalId;
        $this->secret = $secret;
        $this->paymentUrl = $paymentUrl;
    }

    /**
     * Return payment link for user redirection and params for it
     *
     * @param array $params
     * @return array
     * @throws TinkoffException
     */
    public function initPayment(array $params) {
        $requestParams = array(
            'TerminalKey' => $this->terminalId,
            'Amount' => $params['amount'],
            'OrderId' => $params['orderId'],
        );

        $requestParams['Token'] = $this->generateToken($requestParams);

        $initUrl = $this->paymentUrl[strlen($this->paymentUrl)-1] == '/' ? 'Init' : '/Init';
        $url = sprintf('%s%s?%s', $this->paymentUrl,$initUrl, http_build_query($requestParams));


        $resultString = file_get_contents($url);

        //log
        $log = '['.date('D M d H:i:s Y',time()).'] ';
        $log.= 'OrderId='. $params['orderId'] ." data from request:\n";
        $log.= $resultString;
        $log.= "\n";
        file_put_contents(dirname(__FILE__)."/../tinkoff.log", $log, FILE_APPEND);
        
        if(!$this->isJson($resultString)){
            throw new TinkoffException('Не удалось соединиться с платёжным сервисом.');
        }

        $result = json_decode($resultString, true);

        $this->isRequestSuccess($result['Success']);

        if($result['Amount'] != $params['amount']){
            throw new TinkoffException(sprintf('Сумма заказа не сходится. Ответ сервиса: %s', $resultString));
        }

        $url = parse_url($result['PaymentURL']);

        $urlParams = array();

        parse_str($url['query'], $urlParams);

        $this->paymentStatus = $result['Status'];
        $this->paymentId = $result['PaymentId'];

        return array(
            'url' => $result['PaymentURL'],
            'params' => $urlParams,
        );
    }

    /**
     * Recieves notification from TSC, checks is request valid.
     * Should OK in response
     *
     * @param array $params
     * @throws TinkoffException
     */
    public function checkNotification(array $params) {
        $requestParams = $params;
        unset($requestParams['Token']);

        $token = $this->generateToken($requestParams);

        if($params['Token'] != $token){
            throw new TinkoffException(sprintf('Токены не совпадают. Запрос сервиса: %s', serialize($params)));
        }

        $this->isRequestSuccess($requestParams['Success']);

        $this->paymentStatus = $params['Status'];
        $this->paymentId = $params['PaymentId'];
    }

    /**
     * Check if order is complete and money paid
     *
     * @return bool
     * @throws TinkoffException
     */
    public function isOrderPaid(){
        $this->checkStatus();

        return in_array($this->paymentStatus, array(self::STATUS_CONFIRMED));
    }

    /**
     * Checks if oreder is failed
     *
     * @return bool
     */
    public function isOrderFailed(){
        return in_array($this->paymentStatus, array(self::STATUS_CANCELED, self::STATUS_REVERSED, self::STATUS_REJECTED)); //self::STATUS_REFUNDED,
    }
	
	/**
     * Checks if oreder is refunded
     *
     * @return bool
     */
    public function isOrderRefunded(){
        return in_array($this->paymentStatus, array(self::STATUS_REFUNDED));
    }
	
    /**
     * Check is status variable is set
     *
     * @throws TinkoffException
     */
    private function checkStatus(){
        if(is_null($this->paymentStatus)){
            throw new TinkoffException(sprintf('Статус заказа не определён. Чтобы запросить статус вызовите метод getStatus'));
        }
    }

    /**
     * Check bank response format
     *
     * @param $string
     * @return bool
     */
    private function isJson($string) {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Generates request signature
     *
     * @param array $params
     * @return string
     */
    private function generateToken(array $params){
        $requestParams = $params;
        $requestParams['Password'] = $this->secret;

        ksort($requestParams);

        $values = implode('', array_values($requestParams));

        return hash('sha256', $values);
    }

    /**
     * Checks request success
     *
     * @param $success
     * @throws TinkoffException
     */
    private function isRequestSuccess($success){
        if($success == false){
            throw new TinkoffException(sprintf('Не удалось отправить запрос.'));
        }
    }
}