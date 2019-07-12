<?
include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
foreach($_COOKIE as $i=>$val) if(preg_match("#order_#",$i)) $_COOKIE[preg_replace("#order_#","",$i)] = $val;
$template = '/bitrix/templates/iarga.shopplus100.main';
IncludeTemplateLangFile($template.'/header.php');
include_once($_SERVER['DOCUMENT_ROOT'].$template."/inc/functions.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

// Extract directory from filename
if(!$tf){
	$tf = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']);
	$tfs = explode('/',$tf);
	$tf = str_replace($tfs[sizeof($tfs)-1],'',$tf);
}

// Ищем местоположение
if((int) $_POST['city'] > 0) $_SESSION['location'] = $_POST['city'];
if((int) $_POST['city'] > 0){	
	$db_vars = CSaleLocation::GetList(array("SORT"=>"ASC"),array("ID"=>$_POST['city']))->GetNext();
}else{
	$db_vars = CSaleLocation::GetList(array("SORT"=>"ASC"),array("ID"=>$_SESSION['location']))->GetNext();
}
if(!$db_vars){
	$db_vars = CSaleLocation::GetList(array("ID"=>"ASC"),array())->GetNext();
}

// cобираем цену заказа
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

// Доставки
$delArr = Array();

// Ручные
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
// Автоматические
if($city!='' && $db_vars && $db_vars['ID']!=551){
	$deliveries = CSaleDeliveryHandler::GetList(array("SORT" => "ASC","ID"=>"DESC"),array());
	$params = Array("PRICE"=>$ORDER_PRICE,"WEIGHT"=>$ORDER_WEIGHT,"LOCATION_TO"=>$db_vars['ID'],"LOCATION_FROM"=>COption::GetOptionInt('sale', 'location'));
	while($delivery = $deliveries->GetNext()){
		//$delivery = CSaleDelivery::GetByID($delivery['ID']);
		// Расчёт доставки
		$currency = CSaleLang::GetLangCurrency(SITE_ID);		
		$dbHandler = CSaleDeliveryHandler::GetBySID($delivery['SID']);
		if ($arHandler = $dbHandler->Fetch()){
		  $arProfiles = CSaleDeliveryHandler::GetHandlerCompability($params, $delivery);
		  if (is_array($arProfiles) && count($arProfiles) > 0){
			$arProfiles = array_keys($arProfiles);

			$arReturn = CSaleDeliveryHandler::CalculateFull(
			  $delivery['SID'], // идентификатор службы доставки
			  $arProfiles[0], // идентификатор профиля доставки
			  $params, // заказ
			  $currency // валюта, в которой требуется вернуть стоимость
			);
			$debug = 0;
			if ($arReturn["RESULT"] == "OK"){
			  $price = $arReturn["VALUE"];		  
			  if (is_set($arReturn['TRANSIT']) && $arReturn['TRANSIT'] > 0){
				$delivery['DESCRIPTION'] .= '<br>Длительность доставки: '.$arReturn['TRANSIT'].' дней.<br />';
			  }
			}else{
			  if($debug) print '<br>'.('Не удалось рассчитать стоимость доставки! '.$arResult['ERROR']);
			  continue;
			}
		  }else{
			if($debug) print '<br>'.('Невозможно доставить заказ!');
			continue;
		  }
		}
		else{
		   if($debug) print '<br>'.('Обработчик не найден!');
			continue;
		}

		
		$delArr[] = Array(
			"ID"=>$delivery['SID'],
			"PRICE"=>round5($price),
			"NAME"=>$delivery['NAME'],
			"DESCRIPTION"=>$delivery['DESCRIPTION'],
			"AUTO"=>"Y",
			"needpay"=>($delivery['SORT']<=100),
		);
	}
}
?>
<?if(sizeof($delArr)>0):   ?>
	<div class="hr"></div>
	<dl>
		<dt><?=GetMessage("DELIVERY_")?></dt>
		<dd>
			<?foreach($delArr as $i=>$delivery):
				if($delivery['ID']=='firstclass' && sizeof($delArr)>1) continue;
				//if(preg_match("#[^0-9]#",$delivery['ID']) && $delivery['PRICE']<1) continue;
				?>
				<label>
					<span class="input">
						<input type="radio" class="styled" data-rel="<?=ceil($delivery['PRICE'])?>" value="<?=$delivery['ID']?>" <?=(sizeof($delArr)<=1 || $delivery['ID']==$_COOKIE['delivery'])?'checked':''?> name="delivery">
					</span>
					<span class="description">
						<strong><?=$delivery['NAME']?><?=($delivery['PRICE']>0)?' ('.ceil($delivery['PRICE']).GetMessage('VALUTE_SMALL').')':''?></strong><?=($delivery['DESCRIPTION']!='')?''.$delivery['DESCRIPTION'].'':''?></span>
					</span>
				</label>
				<?if($i<sizeof($delArr)-1):?><div class="hr"></div><?endif;?>
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