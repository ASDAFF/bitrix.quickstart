<?
class robokassa {

	function __construct(){
		$this->SetOptions(array(),true);
	}
	
	private $OPTIONS=array();
	
	//Запись ошибок во временный буфер
	public $LAST_ERROR=array();
	private function SetError($error){
		$this->LAST_ERROR[count($this->LAST_ERROR)]=$error;
	}
	
	//Поиск и установка параметров из $_REQUEST
	
	//Генерирует одноразовый пароль
	function GeneratePassword($length = 6) {
		$chars = '0123456789';
		$count = mb_strlen($chars);
		for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= mb_substr($chars, $index, 1);
		}
		return strtoupper($result);
	}
		
	//Устанавливаем параметры
	function SetOptions($arOptions, $request=false){
		
		if (is_array($arOptions)){
			$this->OPTIONS=array_merge($this->OPTIONS, $arOptions);
		} 
		
		if ($request){
			if (!empty($_REQUEST["Shp_fields"])){
				$arOptions=unserialize(base64_decode($_REQUEST["Shp_fields"]));
				if($arOptions){$this->OPTIONS=array_merge($this->OPTIONS, $arOptions);}
			}
		}
		return true;
	}
	
	//Возвращает параметры
	function GetOptions($ItemOptions=''){
		if (empty($ItemOptions)){
			return 	$this->OPTIONS;
		} else {
			return 	$this->OPTIONS[$ItemOptions];
		}
	}
	
	//Возвращает эелемент информационного блока по его ID
	private function GetElementByID($ElementID, $Option){
		if(!empty($Option) and !is_array($Option)){
			$Option=explode(",", $Option);
		}
		if (!empty($ElementID)){
			if (!CModule::IncludeModule("iblock")) {
				return false;
			} 
			$obElements = CIBlockElement::GetByID($ElementID, $Option);
			$arElements = array();
			if ($obElement = $obElements->GetNextElement()) {
				if (!$Option or in_array('FIELD', $Option)){$arElements['FIELD']=$obElement->GetFields();}
				if (!$Option or in_array('PROPERTY', $Option)){$arElements['PROPERTY']=$obElement->GetProperties();}
			}
			return $arElements;
		} else {
			return false;
		}
	}
	
	//Регистрация заказа и переход к его оплате
	function Buy($PRODUCT_ID, $CUSTOMER){
		if (!CModule::IncludeModule("iblock")) return; 
		
		//Получаем поля и свойства продукта ($PRODUCT_ID)
		$obProducts = CIBlockElement::GetByID($PRODUCT_ID);
		$arProducts = array();
		if ($obProduct = $obProducts->GetNextElement()) {
			$arProducts['FIELD']=$obProduct->GetFields();
			$arProducts['PROPERTY']=$obProduct->GetProperties();
		}
		//Добавляем запись о заказе в ИБ
		$arProperties = array();
		$arProperties[$this->OPTIONS['ORDERS']['PROPERTY_PRODUCT_ID']] = $PRODUCT_ID;
		$arProperties[$this->OPTIONS['ORDERS']['PROPERTY_CUSTOMER_NAME']] = $CUSTOMER['NAME'];
		$arProperties[$this->OPTIONS['ORDERS']['PROPERTY_CUSTOMER_PHONE']] = $CUSTOMER['PHONE'];
		$arProperties[$this->OPTIONS['ORDERS']['PROPERTY_CUSTOMER_EMAIL']] = $CUSTOMER['EMAIL'];
		$arProperties[$this->OPTIONS['ORDERS']['PROPERTY_CUSTOMER_MESSAGE']] = $CUSTOMER['MESSAGE'];
		$arProperties[$this->OPTIONS['ORDERS']['PROPERTY_SUM']] = $arProducts['PROPERTY'][$this->OPTIONS['PRODUCTS']['PROPERTY_COST']]['VALUE'];
		
		$PROPERTY_PAYMENT_STATUS_ENUMS = CIBlockPropertyEnum::GetList(array(), array("IBLOCK_ID"=>$this->OPTIONS['ORDERS']['IBLOCK_ID'], "CODE"=>$this->OPTIONS['ORDERS']['PROPERTY_PAYMENT_STATUS']));
		while($PROPERTY_PAYMENT_STATUS_FIELD =$PROPERTY_PAYMENT_STATUS_ENUMS->GetNext()){
			if ($PROPERTY_PAYMENT_STATUS_FIELD['XML_ID']=='N'){
				$arProperties[$this->OPTIONS['ORDERS']['PROPERTY_PAYMENT_STATUS']]=array('N' => $PROPERTY_PAYMENT_STATUS_FIELD['ID']);
			} 
		}
		
		$arOptionNewOrder = array(
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => $this->OPTIONS['ORDERS']['IBLOCK_ID'],
			"PROPERTY_VALUES" => $arProperties,
			"NAME" => $arProducts['FIELD']['NAME'],
			"ACTIVE" => "Y"
		);
		
		$obAddOrder=new CIBlockElement();
		$resAddOrder=$obAddOrder->Add($arOptionNewOrder);
		if ($resAddOrder) {
			//Записываем данные заказчика в параметры
			$this->OPTIONS['CUSTOMER']=array(
				'NAME'=>$CUSTOMER['NAME'],
				'PHONE'=>$CUSTOMER['PHONE'],
				'EMAIL'=>$CUSTOMER['EMAIL'],
				'MESSAGE'=>$CUSTOMER['MESSAGE']
			);
			
			//Дописываем в параметры информацию по продукту
			$this->OPTIONS['PRODUCTS']['ID']=$PRODUCT_ID;
			$this->OPTIONS['PRODUCTS']['NAME']=$arProducts['FIELD']['NAME'];
			
			//Дописываем в параметры информацию о дате заказа (текущая дата)
			$this->OPTIONS['ORDERS']['DATE']= time();
			
			//Формируем данные и выполняем редирект на Robokassa
			$login = $this->OPTIONS['ACCOUNT']['LOGIN'];
			$password = $this->OPTIONS['ACCOUNT']['PASSWORD'];
			$constanta = 1;
			$cost = $arOptionNewOrder['PROPERTY_VALUES'][$this->OPTIONS['ORDERS']['PROPERTY_SUM']];
			$cost = str_replace(" ","",$cost);
			$parametrs = $this->OPTIONS;
			unset($parametrs['ACCOUNT']);
			$parametrs=base64_encode(serialize($parametrs));
			
			$SignatureValue= md5($login.':'.$cost.':'.$resAddOrder.':'.$password.':Shp_fields='.$parametrs.':Shp_item='.$constanta);
			$description = $arProducts['FIELD']['NAME'];
			$payment_url='https://auth.robokassa.ru/Merchant/Index.aspx';
			if($this->OPTIONS['PAYMENT_TEST_MODE']=='Y'){				
				$IsTest=1;
			} else {
				$IsTest=0;
			}
			
			?>
			
			<form method="get" action="<?=$payment_url?>" style="display:none;" id="rk-form">
				<input type="hidden" name="MrchLogin" value="<?=$login;?>" />
				<input type="hidden" name="OutSum" value="<?=$cost;?>" />
				<input type="hidden" name="InvId" value="<?=$resAddOrder;?>" />
				<input type="hidden" name="Shp_item" value="<?=$constanta;?>" />
				<input type="hidden" name="SignatureValue" value="<?=$SignatureValue;?>" />
				<input type="hidden" name="Desc" value="<?=$description;?>" />
				<input type="hidden" name="Shp_fields" value="<?=$parametrs;?>" />
                <input type="hidden" name="IsTest" value="<?=$IsTest;?>" />
			</form>
			<script>window.onload=function(){document.getElementById('rk-form').submit();}</script>
			
			<?
			
		} else {
			$this->SetError($obAddOrder->LAST_ERROR);
			return $resAddOrder;
		}
	}
	
	
	//Выполняет подтверждение заказа, установка статуса и одноразового пароля.
	function CheckPayment(){
		$password = $this->OPTIONS['ACCOUNT']['PASSWORD'];
		$cost = $_REQUEST["OutSum"];
		$order_id = $_REQUEST["InvId"];
		$constanta = $_REQUEST["Shp_item"];
		$fields = $_REQUEST["Shp_fields"];
		$SignatureValue = strtoupper($_REQUEST["SignatureValue"]);
		$control_hash = strtoupper(md5($cost.':'.$order_id.':'.$password.':Shp_fields='.$fields.':Shp_item='.$constanta));
		if ($control_hash != $SignatureValue) {
			return false;
		} else {
			
			//Получаем информацию по платежу
			$arPayment=array('OutSum'=>$_REQUEST['OutSum'], 'InvId'=>$_REQUEST['InvId']);
			
			//Получаем параметры из $_REQUEST добавляем в массив оций текущего объекта
			$this->OPTIONS=array_merge($this->OPTIONS, array('PAYMENT'=>$arPayment));
			
			return true;	
		}
	}
	
	
	function CheckOrder(){
		//Изменяем статус заказа
		if (!CModule::IncludeModule("iblock")) return false;
		$PAYMENT_STATUS='Y';
		$PROPERTY_PAYMENT_STATUS_ENUMS = CIBlockPropertyEnum::GetList(array(), array("IBLOCK_ID"=>$this->OPTIONS['ORDERS']['IBLOCK_ID'], "CODE"=>$this->OPTIONS['ORDERS']['PROPERTY_PAYMENT_STATUS']));
		while($PROPERTY_PAYMENT_STATUS_FIELD =$PROPERTY_PAYMENT_STATUS_ENUMS->GetNext()){
			if ($PROPERTY_PAYMENT_STATUS_FIELD['XML_ID']=='Y'){
				$PAYMENT_STATUS=array('Y' => $PROPERTY_PAYMENT_STATUS_FIELD['ID']);
			} 
		}
		CIBlockElement::SetPropertyValueCode($this->OPTIONS['PAYMENT']['InvId'], $this->OPTIONS['ORDERS']['PROPERTY_PAYMENT_STATUS'], $PAYMENT_STATUS);
		
		//Генерируем и задаем пароль
		$NewPassword=$this->GeneratePassword();
		CIBlockElement::SetPropertyValueCode($this->OPTIONS['PAYMENT']['InvId'], $this->OPTIONS['ORDERS']['PROPERTY_PASSWORD'], $NewPassword);
		$this->OPTIONS['ORDERS']['PASSWORD']=$NewPassword;
		
		return true;	
	}
	
	//Получает информацию о продукте
	function GetProduct($Option){
		return $this->GetElementByID($this->OPTIONS['PRODUCTS']['ID'], $Option);
	}
	
	//Получаем информацию о заказе (При перехвате ответа от РК)
	function GetOrder($Option){
		return $this->GetElementByID($this->OPTIONS['PAYMENT']['InvId'], $Option);
	}
	
	//Получение заказа по паролю
	function GetOrderByPassword($OrderID, $Password, $Option){
		$arOrder=$this->GetElementByID($OrderID, $Option);
		if ($Password == $arOrder['PROPERTY'][$this->OPTIONS['ORDERS']['PROPERTY_PASSWORD']]['VALUE']){
			$arProduct=$this->GetElementByID($arOrder['PROPERTY'][$this->OPTIONS['ORDERS']['PROPERTY_PRODUCT_ID']]['VALUE']);
			$arResult['ORDER']=$arOrder;
			$arResult['PRODUCT']=$arProduct;
			return 	$arResult;
		} else {
			false;
		}
	}
}

?>
