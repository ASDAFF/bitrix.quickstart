<?
include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
foreach($_COOKIE as $i=>$val) if(preg_match("#order_#",$i)) $_COOKIE[preg_replace("#order_#","",$i)] = $val;
__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/template.php');

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");
CModule::IncludeModule("iarga.cleverbasket");

// Extract directory from filename
if(!$templateFolder){
	$templateFolder = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']);
	$templateFolders = explode('/',$templateFolder);
	$templateFolder = str_replace($templateFolders[sizeof($templateFolders)-1],'',$templateFolder);
}

// ���� ��������������
if(isset($_POST['city'])) $_SESSION['location'] = $_POST['city'];
if(isset($_POST['city'])){	
	$db_vars = CSaleLocation::GetList(array("SORT"=>"ASC"),array("ID"=>$_POST['city']))->GetNext();
}else{
	$db_vars = CSaleLocation::GetList(array("SORT"=>"ASC"),array("ID"=>$_SESSION['location']))->GetNext();
}
if(!$db_vars){
	$db_vars = CSaleLocation::GetList(array("ID"=>"ASC"),array())->GetNext();
}

// ������� ���� ������
$ORDER_PRICE = 0;
$ORDER_WEIGHT = 0;
$b_list = CSaleBasket::GetList(Array(),Array("ORDER_ID"=>"NULL","FUSER_ID"=>CSaleBasket::GetBasketUserID()));
while($b = $b_list->GetNext()){
	$ORDER_PRICE += $b['QUANTITY'] * $b['PRICE'];
	$ORDER_WEIGHT += $b['QUANTITY'] * $b['WEIGHT'];
}
//print_r($db_vars);
$cond = Array(
	"ACTIVE"=>"Y",
	"LID"=>SITE_ID,
	//"+<=ORDER_PRICE_FROM" => $ORDER_PRICE,
	//"+>=ORDER_PRICE_TO" => $ORDER_PRICE,
	//"+<=WEIGHT_FROM"=>$ORDER_WEIGHT,
	//"+>=WEIGHT_TO"=>$ORDER_WEIGHT,
	"LOCATION"=>$db_vars['ID'],
);

// ��������
$delArr = Array();

// ������
$deliveries = CSaleDelivery::GetList(Array("SORT"=>"ASC"),$cond);
while($delivery = $deliveries->GetNext()){
	$delivery = CSaleDelivery::GetByID($delivery['ID']);
	if($delivery['ORDER_PRICE_FROM'] > 0 && $delivery['ORDER_PRICE_FROM'] > $ORDER_PRICE) continue;
	if($delivery['ORDER_PRICE_TO'] > 0 && $delivery['ORDER_PRICE_TO'] < $ORDER_PRICE) continue;
	if($delivery['WEIGHT_FROM'] > 0 && $delivery['WEIGHT_FROM'] > $ORDER_WEIGHT) continue;
	if($delivery['WEIGHT_TO'] > 0 && $delivery['WEIGHT_TO'] < $ORDER_WEIGHT) continue;

	$delArr[] = Array(
		"ID"=>$delivery['ID'],
		"PRICE"=>$delivery['PRICE'],
		"NAME"=>$delivery['NAME'],
		"DESCRIPTION"=>$delivery['DESCRIPTION'],
		"AUTO"=>"N",
		"needpay"=>$needapay,
	);
}
// ��������������
if($city!='' && $db_vars && $db_vars['ID']!=551){
	$deliveries = CSaleDeliveryHandler::GetList(array("SORT" => "ASC","ID"=>"DESC"),array());
	$params = Array("PRICE"=>$ORDER_PRICE,"WEIGHT"=>$ORDER_WEIGHT,"LOCATION_TO"=>$db_vars['ID'],"LOCATION_FROM"=>COption::GetOptionInt('sale', 'location'));
	while($delivery = $deliveries->GetNext()){
		//$delivery = CSaleDelivery::GetByID($delivery['ID']);
		// ������ ��������
		$currency = CSaleLang::GetLangCurrency(SITE_ID);		
		$dbHandler = CSaleDeliveryHandler::GetBySID($delivery['SID']);
		if ($arHandler = $dbHandler->Fetch()){
		  $arProfiles = CSaleDeliveryHandler::GetHandlerCompability($params, $delivery);
		  if (is_array($arProfiles) && count($arProfiles) > 0){
			$arProfiles = array_keys($arProfiles);

			$arReturn = CSaleDeliveryHandler::CalculateFull(
			  $delivery['SID'], // ������������� ������ ��������
			  $arProfiles[0], // ������������� ������� ��������
			  $params, // �����
			  $currency // ������, � ������� ��������� ������� ���������
			);
			$debug = 0;
			if ($arReturn["RESULT"] == "OK"){
			  $price = $arReturn["VALUE"];		  
			  if (is_set($arReturn['TRANSIT']) && $arReturn['TRANSIT'] > 0){
				$delivery['DESCRIPTION'] .= '<br>������������ ��������: '.$arReturn['TRANSIT'].' ����.<br />';
			  }
			}else{
			  if($debug) print '<br>'.('�� ������� ���������� ��������� ��������! '.$arResult['ERROR']);
			  continue;
			}
		  }else{
			if($debug) print '<br>'.('���������� ��������� �����!');
			continue;
		  }
		}
		else{
		   if($debug) print '<br>'.('���������� �� ������!');
			continue;
		}

		
		$delArr[] = Array(
			"ID"=>$delivery['SID'],
			"PRICE"=>$price,
			"NAME"=>$delivery['NAME'],
			"DESCRIPTION"=>$delivery['DESCRIPTION'],
			"AUTO"=>"Y",
			"needpay"=>($delivery['SORT']<=100),
		);
	}
}
?>

<?if(sizeof($delArr)>0):   ?>
	<div class="hr_ia"></div>
	<dl>
		<dt><?=GetMessage("DELIVERY_")?></dt>
		<dd>
			<?foreach($delArr as $i=>$delivery):
				if($delivery['ID']=='firstclass' && sizeof($delArr)>1) continue;
				//if(preg_match("#[^0-9]#",$delivery['ID']) && $delivery['PRICE']<1) continue;
				?>
				<label>
					<span class="input_ia">
						<input type="radio" class="styled" data-rel="<?=ceil($delivery['PRICE'])?>" value="<?=$delivery['ID']?>" <?=(sizeof($delArr)<=1 || $delivery['ID']==$_COOKIE['delivery'])?'checked':''?> name="delivery">
					</span>
					<span class="description_ia">
						<strong><?=$delivery['NAME']?><?=($delivery['PRICE']>0)?' ('.ceil($delivery['PRICE']).GetMessage('VALUTE_SMALL').')':''?></strong><?=($delivery['DESCRIPTION']!='')?''.$delivery['DESCRIPTION'].'':''?></span>
					</span>
				</label>
				<?if($i<sizeof($delArr)-1):?><div class="hr_ia"></div><?endif;?>
			<?endforeach?>
		</dd>
	</dl>
<?elseif($_POST['city']!=''):?>
	<dl>
		<dt></dt>
		<dd>
			<?=GetMessage("NO_CITY")?>
		</dd>
	</dl>
<?else:?>
	<input type="hidden" data-rel="0" value="0">
<?endif;?>