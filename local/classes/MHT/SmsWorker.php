<?

	namespace MHT;

	use Sms;
	use CModule;
	use CSaleOrder;
	use CSaleOrderPropsValue;

	class SmsWorker{
		protected static $instance = null;
		static function getInstance(){
			if(self::$instance === null){
				self::$instance = new static;
			}
			return self::$instance;
		}

		protected $orderId;
		protected $statusId;
		protected function __construct(){}

		function send(){
			$this->log(__FUNCTION__);
			$orderId = $this->getOrderId();
			$statusId = $this->getStatusId();
			$this->log($orderId);
			$this->log($statusId);

			if(empty($orderId) || empty($statusId)){
				return $this;
			}

			$order = $this->getOrder();
			$paySystemId = $order['PAY_SYSTEM_ID'];
			$isRobokassa = ($paySystemId == 17);
			$sms         = new Sms();
			$sms->setPhone($order['PROPERTIES']['PHONE']);
			$this->log($order);

			

			switch($statusId){
				case 'N': // Принят, ожидается оплата
					if($isRobokassa){
						// email
						break;
					}
					if($order['PROPERTIES']['NEW_SMS_SENDED']=='Y'){
						$this->log("Смс уже отправлена. Y");
						return $this;
					}else{
						$this->log("Смс ещё не отправлена.");
						UpdateOrderProperty("NEW_SMS_SENDED", "Y", $orderId);
					}
					
					//$sms->setText('moshoztorg.ru: ваш заказ №'.$orderId.' принят. Спасибо за заказ!');
					$sms->setText(
"MHT.RU : Ваш заказ №{$orderId} принят.
Наш телефон:8-800-550-47-47"
					);
					// email, sms
					break;

				/*case 'F': // Выполнен
					$sms->setText(
'Ваш заказ №'.$orderId.' выполнен.
Спасибо Вам за обращение в наш интернет-магазин.
MHT.RU						
');
					// sms
break;*/

				case 'H': // Заказ принят в транспортную компанию
					$sms->setText(
'Ваш заказ №'.$orderId.' передан в транспортную службу.
Вам придет СМС когда посылка будет передана в доставку. При получении обязательно проверяйте целостность упаковки!
MHT.RU');
					// sms
					break;


				case 'P': //Формируется к отправке
				$sms->setText('Ваш заказ №'.$orderId.' формируется. Наш телефон:8-800-550-47-47');
					// sms, email
					break;

				/*case 'W': // Заказ на пункте самовывоза
					$sms->setText(
'Ваш заказ №'.$orderId.' в пункте самовывоза.
Вы можете забрать Ваш заказ в часы работы магазина в течение 7 дней.
MHT.RU');
					// sms, email
				break;*/

				case "Z":
				$sms->setText(
'Уважаемый клиент.
Ваш заказ №'.$orderId.' не может быть обработан в связи с тем что телефонный номер указанный в заказе не отвечает или недоступен.
Просим Вас связаться с нами по телефону:
8-800-550-47-47
Или по E-mail:
order@mht.ru
При отсутствии обратной связи ваш заказ будет отменен через 2 дня.
Спасибо.
MHT.RU');
				break;

			}

			if(
				$sms->getText() &&
				$sms->getPhone()
			){
				$sms->send();
			}

			return $this;
		}

		protected function log($data){
			ob_start();

			\WP::log($data);

			file_put_contents(
				__DIR__.'/a.log',
				ob_get_clean(),
				FILE_APPEND
			);
		}

		protected function getOrder(){
			CModule::IncludeModule('sale');
			$arOrder = CSaleOrder::GetByID($this->orderId);
			$arOrder['PROPERTIES'] = array();
			$ob2 = CSaleOrderPropsValue::GetList(array(), array("ORDER_ID" => $this->orderId));
			while($arProps = $ob2->Fetch()){
				$arOrder['PROPERTIES'][$arProps['CODE']] = $arProps['VALUE'];
			}
			return $arOrder;
		}

		// get set
		// 
		
		public function getOrderId(){
		    return $this->orderId;
		}
		
		public function setOrderId($orderId){
			$this->log(__FUNCTION__);
		    $this->orderId = $orderId;
		    return $this;
		}

		public function getStatusId(){
		    return $this->statusId;
		}
		
		public function setStatusId($statusId){
			$this->log(__FUNCTION__);
		    $this->statusId = $statusId;
		    return $this;
		}
	}