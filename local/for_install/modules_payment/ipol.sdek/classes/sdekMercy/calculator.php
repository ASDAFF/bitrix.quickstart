<?php
/**
 * Расчёт стоимости доставки СДЭК
 * Модуль для интернет-магазинов (ИМ)
 * 
 * @version 1.0
 * @since 21.06.2012
 * @link http://www.edostavka.ru/integrator/
 * @see 3197
 * @author Tatyana Shurmeleva, live long and prosper
 */
class CalculatePriceDeliverySdek {
	private $version = "1.0";//версия модуля
    private $jsonUrl = 'http://api.cdek.ru/calculator/calculate_price_by_json_request.php';

	private $authLogin;
	private $authPassword;

	private $senderCityId;
	private $receiverCityId;
	private $tariffId;
	private $modeId;
	public $goodsList;
	public $tariffList;
	private $result;
    private $error;
	public $dateExecute;
	private $timeOut;

	public function __construct(){
	     $this->dateExecute = date('Y-m-d');
	}

	public function setDateExecute($date){
		$this->dateExecute = date($date);
	}

	public function setAuth($authLogin,$authPassword){
		$this->authLogin    = $authLogin;
		$this->authPassword = $authPassword;
	}

	private function _getSecureAuthPassword(){
		return md5($this->dateExecute.'&'.$this->authPassword);
	}

	public function setSenderCityId($id){
		$id = (int) $id;
		if(!$id)
			throw new Exception(GetMessage("IPOLSDEK_CALCEXC_WRONGSENDER"));
		$this->senderCityId = $id;
	}

	public function setReceiverCityId($id) {
		$id = (int) $id;
		if(!$id)
			throw new Exception(GetMessage("IPOLSDEK_CALCEXC_WRONGRESEWER"));
		$this->receiverCityId = $id;
	}

	public function setTariffId($id) {
		$id = (int) $id;
		if(!$id)
			throw new Exception(GetMessage("IPOLSDEK_CALCEXC_WRONGTARIF"));
		$this->tariffId = $id;
	}

	public function setModeDeliveryId($id) {
		$id = (int) $id;
		if(!in_array($id,array(1,2,3,4)))
			throw new Exception(GetMessage("IPOLSDEK_CALCEXC_WRONGDELIVTR"));
		$this->modeId = $id;
	}

	public function addGoodsItemBySize($weight, $length, $width, $height) {
		//проверка веса
		$weight = (float) $weight;
		if(!$weight)
			throw new Exception(GetMessage("IPOLSDEK_CALCEXC_WRONGOPN").(count($this->getGoodslist())+1).".");
		//проверка остальных величин
		$paramsItem = array(GetMessage("IPOLSDEK_CALCEXC_length")  => $length, 
							GetMessage("IPOLSDEK_CALCEXC_width")   => $width, 
							GetMessage("IPOLSDEK_CALCEXC_height")  => $height);
		foreach($paramsItem as $k=>$param) {
			$param = (int) $param;
			if(!$param)
				throw new Exception(GetMessage("IPOLSDEK_CALCEXC_WRONGPAR")."'".$k."'".GetMessage("IPOLSDEK_CALCEXC_ofplace").(count($this->getGoodslist())+1).".");
		}
		$this->goodsList[] = array( 'weight' 	=> $weight, 
									'length' 	=> $length,
									'width' 	=> $width,
									'height' 	=> $height);
	}

	public function addGoodsItemByVolume($weight, $volume) {
		$paramsItem = array(GetMessage("IPOLSDEK_CALCEXC_weight")    => $weight, 
							GetMessage("IPOLSDEK_CALCEXC_weightVol") => $volume);
		foreach($paramsItem as $k=>$param){
			$param = (float) $param;
			if($param == 0.00)
				throw new Exception(GetMessage("IPOLSDEK_CALCEXC_WRONGPAR")."'".$k."'".GetMessage("IPOLSDEK_CALCEXC_ofplace").(count($this->getGoodslist())+1) . ".");
		}
		$this->goodsList[] = array( 'weight' 	=> $weight, 
									'volume'	=> $volume );
	}

	public function getGoodslist() {
		if(!isset($this->goodsList))
			return NULL;
		return $this->goodsList;
	}

	public function addTariffPriority($id,$priority = 0){
		$id = (int) $id;
		if($id == 0)
			throw new Exception(GetMessage("IPOLSDEK_CALCEXC_WRONGTARFID"));
        $priority = ($priority > 0) ? $priority : count($this->tariffList)+1;
		$this->tariffList[] = array( 'priority' => $priority,
									 'id' 		=> $id);
	}

	private function _getTariffList(){
		if(!isset($this->tariffList))
			return NULL;
		return $this->tariffList;
	}

	private function _getRemoteData($data){
        $bodyData = array (
          'json' => json_encode($data)
        );
        $data_string = http_build_query($bodyData);

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $this->jsonUrl);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
		    'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen($data_string)
            ) 
		);
		curl_setopt($ch, CURLOPT_TIMEOUT,$this->timeOut);

		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);

		$result = curl_exec($ch); 
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if($code == 200)
			return json_decode($result, true);
		elseif($code == 0)
			return array('noanswer'=>true);
		else 
			return "Wrong answer ".$code;
	}

	public function calculate() {
		$data = array();
		$data['dateExecute'] = $this->dateExecute;
		isset($this->version)        ? $data['version']        = $this->version                  : '';		
		isset($this->authLogin)      ? $data['authLogin']      = $this->authLogin                : '';
		isset($this->authPassword)   ? $data['secure']         = $this->_getSecureAuthPassword() : '';
		isset($this->senderCityId)   ? $data['senderCityId']   = $this->senderCityId             : '';
		isset($this->receiverCityId) ? $data['receiverCityId'] = $this->receiverCityId           : '';
		isset($this->tariffId)       ? $data['tariffId']       = $this->tariffId                 : '';
		isset($this->tariffList)     ? $data['tariffList']     = $this->tariffList               : '';
		isset($this->modeId)         ? $data['modeId']         = $this->modeId                   : '';
		isset($this->timeOut) ? '' : $this->timeOut = 6;

		if(isset($this->goodsList)){
			foreach ($this->goodsList as $idGoods => $goods) {
				$data['goods'][$idGoods] = array();
				(isset($goods['weight']) && $goods['weight'] <> '' && $goods['weight']>0) ? $data['goods'][$idGoods]['weight'] = $goods['weight'] : '';
				(isset($goods['length']) && $goods['length'] <> '' && $goods['length']>0) ? $data['goods'][$idGoods]['length'] = $goods['length'] : '';
				(isset($goods['width'])  && $goods['width']  <> '' && $goods['width']>0)  ? $data['goods'][$idGoods]['width']  = $goods['width']  : '';
				(isset($goods['height']) && $goods['height'] <> '' && $goods['height']>0) ? $data['goods'][$idGoods]['height'] = $goods['height'] : '';
				(isset($goods['volume']) && $goods['volume'] <> '' && $goods['volume']>0) ? $data['goods'][$idGoods]['volume'] = $goods['volume'] : '';

			}
		}
		//проверка на подключние библиотеки curl
		if(!extension_loaded('curl'))
			throw new Exception(GetMessage("IPOLSDEK_CALCEXC_NOCURL"));

        /**
         * перебираем все тарифы и делаем запрос по каждому из них.
         * потом выбираем самый дешёвый
         */
		if(count($data['tariffList']) > 1){
		    $tariffList = $data['tariffList'];
		    $minPrice = 0;
		    $lastResponse = [];
		    foreach ($tariffList as $tariff){
                $data['tariffList'] = [];
                $data['tariffList'][] = $tariff;
                $response = $this->_getRemoteData($data);
                if(isset($response['result']) && !empty($response['result'])){
                    if(
                        ($response['result']['priceByCurrency'] < $minPrice && $minPrice > 0) ||
                        $minPrice === 0
                    ){
                        $minPrice = $response['result']['priceByCurrency'];
                        $lastResponse = $response;
                    }
                }
            }
            $response = $lastResponse;
        } else {
            $response = $this->_getRemoteData($data);
        }

        if(isset($response['result']) && !empty($response['result'])){
            $this->result = $response;
            return true;
        }elseif(isset($response['noanswer']) && !empty($response['noanswer'])){
			$this->result = 'noanswer';
			return false;
		}else{
            $this->error = $response;
            return false;
        }
        
		//return (isset($response['result']) && (!empty($response['result']))) ? true : false;
		//результат
		//$result = ($this->getResponse());
		//return $result;
	}

	public function getResult(){
		return $this->result;
	}

	public function getError() {
		return $this->error;
	}
	
	public function setTimeout($val){
		$val = floatval($val);
		$this->timeOut = ($val <= 0) ? 6 : $val;
	}
}

?>