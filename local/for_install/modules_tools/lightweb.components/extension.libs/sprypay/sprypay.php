<?
class sprypay {

	function __construct(){
		$this->SetOptions(array(),true);
	}
	
	private $OPTIONS=array();
	
	//������ ������ �� ��������� �����
	public $LAST_ERROR=array();
	private function SetError($error){
		$this->LAST_ERROR[count($this->LAST_ERROR)]=$error;
	}
	
	//���������� ����������� ������
	function GeneratePassword($length = 6) {
		$chars = '0123456789';
		$count = mb_strlen($chars);
		for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= mb_substr($chars, $index, 1);
		}
		return strtoupper($result);
	}
	
	//������������� ���������
	function SetOptions($arOptions, $request=false){
		
		if (is_array($arOptions)){
			$this->OPTIONS=array_merge($this->OPTIONS, $arOptions);
		}
		
		if ($request){
			if (!empty($_REQUEST["spUserData_0"])){
				
				$data = $_POST;
				for ($i = 0; $i<count($data)-17; $i++)
				{
					$arOptions .= $_REQUEST["spUserData_"."$i"];
				}
				$arOptions = substr($arOptions, 5, strlen($arOptions)-5);
				$arOptions=unserialize(base64_decode($arOptions));
				
				if($arOptions){$this->OPTIONS=array_merge($this->OPTIONS, $arOptions);}
			}
		}
		return true;
	}
	
	//���������� ���������
	function GetOptions($ItemOptions=''){
		if (empty($ItemOptions)){
			return 	$this->OPTIONS;
		} else {
			return 	$this->OPTIONS[$ItemOptions];
		}
	}
	
	//���������� �������� ��������������� ����� �� ��� ID
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
	
	//����������� ������ � ������� � ��� ������
	function Buy($PRODUCT_ID, $CUSTOMER){
		if (!CModule::IncludeModule("iblock")) return; 
		
		//�������� ���� � �������� �������� ($PRODUCT_ID)
		$obProducts = CIBlockElement::GetByID($PRODUCT_ID);
		$arProducts = array();
		if ($obProduct = $obProducts->GetNextElement()) {
			$arProducts['FIELD']=$obProduct->GetFields();
			$arProducts['PROPERTY']=$obProduct->GetProperties();
		}
		//��������� ������ � ������ � ��
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
			//���������� ������ ��������� � ���������
			$this->OPTIONS['CUSTOMER']=array(
				'NAME'=>$CUSTOMER['NAME'],
				'PHONE'=>$CUSTOMER['PHONE'],
				'EMAIL'=>$CUSTOMER['EMAIL'],
				'MESSAGE'=>$CUSTOMER['MESSAGE']
			);
			
			//���������� � ��������� ���������� �� ��������
			$this->OPTIONS['PRODUCTS']['ID']=$PRODUCT_ID;
			$this->OPTIONS['PRODUCTS']['NAME']=$arProducts['FIELD']['NAME'];
			$this->OPTIONS['PRODUCTS']['PRODUCTS_DESCRIPTION']=$arProducts['PROPERTY']['TEXT_FORECAST']['VALUE']['TEXT'];
			
			//���������� � ��������� ���������� � ���� ������ (������� ����)
			$this->OPTIONS['ORDERS']['DATE']= time();
			
			//��������� ������ � ��������� �������� �� SpryPay
			$login = $this->OPTIONS['ACCOUNT']['LOGIN'];
			$description = $arProducts['FIELD']['NAME'];
			$password = $this->OPTIONS['ACCOUNT']['PASSWORD'];
			$constanta = 1;
			$cost = $arOptionNewOrder['PROPERTY_VALUES'][$this->OPTIONS['ORDERS']['PROPERTY_SUM']];
			$parametrs = $this->OPTIONS;
			unset($parametrs['ACCOUNT']);
			$parametrs=base64_encode(serialize($parametrs));
			
			if($this->OPTIONS['PAYMENT_TEST_MODE']=='Y'){
				$payment_url='http://sprypay.ru/sppi/';
			} else {
				$payment_url='https://sprypay.ru/sppi/';
			}
			
			$parametrs=str_split($parametrs, 200);
			?>
            
            <form method="post" action="<?=$payment_url?>" style="display:none;" id="sp-form">
                <input type="hidden" name="spShopId" value="<?=$login;?>">
                <input type="hidden" name="spShopPaymentId" value="<?=$resAddOrder;?>">
                <input type="hidden" name="spCurrency" value="<?=$this->OPTIONS['PAYMENT_CURRENCY'];?>">
                <input type="hidden" name="spPurpose" value="<?=$description;?>">
                <input type="hidden" name="spAmount" value="<?=$cost;?>">
            <?
                $part=0;
				foreach ($parametrs as $parametrs_part){?>
					<input type="hidden" name="spUserData_<?=$part;?>" value="<?=$parametrs_part;?>">
				<? 
					$part++;
				}
            ?>
            </form>
            <script>window.onload=function(){document.getElementById('sp-form').submit();}</script>
			<?
		} else {
			$this->SetError($obAddOrder->LAST_ERROR);
			return $resAddOrder;
		}
	}
	
	//��������� ������������� ������, ��������� ������� � ������������ ������.
	function CheckPayment(){
		
		// ������ ����������, ������� ������ �������������� � ������� � ������� �������
		$spQueryFields = array('spPaymentId', 'spShopId', 'spShopPaymentId', 'spBalanceAmount', 'spAmount', 'spCurrency', 'spCustomerEmail', 'spPurpose', 'spPaymentSystemId', 'spPaymentSystemAmount', 'spPaymentSystemPaymentId', 'spEnrollDateTime', 'spHashString', 'spBalanceCurrency');
		
		// ��������, ��� ��� ��� ������������ � �������
		foreach($spQueryFields as $spFieldName) if (!isset($_POST[$spFieldName])) exit("error � ������� � ������� ������� ����������� �������� `$spFieldName`");
		
		// ��� ��������� ����, �������� � ���������� ��������
		$yourSecretKeyString = $this->OPTIONS['ACCOUNT']['PAYMENT_SECRET_KEY'];//"2469a0a8fea64644bc36608fa0c7c8fc";
		// ������� ����������� �������
		$localHashString = md5($_POST['spPaymentId'].$_POST['spShopId'].$_POST['spShopPaymentId'].$_POST['spBalanceAmount'].$_POST['spAmount'].$_POST['spCurrency'].$_POST['spCustomerEmail'].$_POST['spPurpose'].$_POST['spPaymentSystemId'].$_POST['spPaymentSystemAmount'].$_POST['spPaymentSystemPaymentId'].$_POST['spEnrollDateTime'].$yourSecretKeyString);
		
		//������� ���������� ������� � ��, ��� ������ � ��������
		if ($localHashString != $_POST['spHashString']) {			
			return false;
		} else {
			//�������� ���������� �� �������
			$arPayment=array('OutSum'=>$_POST['spAmount'], 'InvId'=>$_POST['spShopPaymentId']);
			
			//�������� ��������� �� $_REQUEST ��������� � ������ ���� �������� �������
			$this->OPTIONS=array_merge($this->OPTIONS, array('PAYMENT'=>$arPayment));
			return true;
		}
	}
	
	function CheckOrder(){
		//�������� ������ ������
		if (!CModule::IncludeModule("iblock")) return false;
		$PAYMENT_STATUS='Y';
		$PROPERTY_PAYMENT_STATUS_ENUMS = CIBlockPropertyEnum::GetList(array(), array("IBLOCK_ID"=>$this->OPTIONS['ORDERS']['IBLOCK_ID'], "CODE"=>$this->OPTIONS['ORDERS']['PROPERTY_PAYMENT_STATUS']));
		while($PROPERTY_PAYMENT_STATUS_FIELD =$PROPERTY_PAYMENT_STATUS_ENUMS->GetNext()){
			if ($PROPERTY_PAYMENT_STATUS_FIELD['XML_ID']=='Y'){
				$PAYMENT_STATUS=array('Y' => $PROPERTY_PAYMENT_STATUS_FIELD['ID']);
			}
		}
		CIBlockElement::SetPropertyValueCode($this->OPTIONS['PAYMENT']['InvId'], $this->OPTIONS['ORDERS']['PROPERTY_PAYMENT_STATUS'], $PAYMENT_STATUS);
		
		//���������� � ������ ������
		$NewPassword=$this->GeneratePassword();
		CIBlockElement::SetPropertyValueCode($this->OPTIONS['PAYMENT']['InvId'], $this->OPTIONS['ORDERS']['PROPERTY_PASSWORD'], $NewPassword);
		$this->OPTIONS['ORDERS']['PASSWORD']=$NewPassword;
		
		return true;
	}
	
	//�������� ���������� � ��������
	function GetProduct($Option){
		return $this->GetElementByID($this->OPTIONS['PRODUCTS']['ID'], $Option);
	}
	
	//�������� ���������� � ������ (��� ��������� ������ �� ��)
	function GetOrder($Option){
		return $this->GetElementByID($this->OPTIONS['PAYMENT']['InvId'], $Option);
	}
	
	//��������� ������ �� ������
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