<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
$template = '/bitrix/templates/iarga.shopplus100.main';
IncludeTemplateLangFile($template.'/header.php');
include($_SERVER['DOCUMENT_ROOT'].$template."/inc/functions.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

// discount
if(isset($_POST['discount_code'])){
	// code
	$c = CCatalogDiscountCoupon::GetList (Array("SORT"=>"ASC"),Array("ACTIVE"=>"Y","COUPON"=>$_POST['discount_code']))->GetNext();
	if($c) $d = CCatalogDiscount::GetByID($c['DISCOUNT_ID']);
	if($d){
		$_SESSION['discount'] = $d['NAME'];
		$_SESSION['discount_val'] = $d['VALUE'];
		$_SESSION['discount_type'] = $d['VALUE_TYPE'];
		$_SESSION['discount_code'] = $_POST['discount_code'];
		$_SESSION['discount_once'] = $c['ONE_TIME'];
	}else{
		$_SESSION['discount'] = '';
		$_SESSION['discount_val'] = '';
		$_SESSION['discount_type'] = '';
		$_SESSION['discount_code'] = '';
		$_SESSION['discount_once'] = "";
	}
}

if(isset($_POST['add'])){
	$num = ($_POST['num']>0)?floor($_POST['num']):0;
	$item = CSaleBasket::GetList(Array(),array("FUSER_ID"=>CSaleBasket::GetBasketUserID(),"LID"=>SITE_ID,"ORDER_ID"=>"NULL","PRODUCT_ID"=>$_POST['add']))->GetNext();
	CSaleBasket::Delete($item['ID']);
	$price = CCatalogProduct::GetOptimalPrice($_POST['add']);
	if($num > 0) Add2Basket($price['PRICE']['ID'],$num);
}
$p = $price;

$list = CSaleBasket::GetList(Array(),Array("FUSER_ID" => CSaleBasket::GetBasketUserID(),"LID" => SITE_ID,"ORDER_ID" => "NULL"));
$price = 0;
$num = 0;
$goods = Array();
while($el = $list->GetNext()){
	$good = CIBlockElement::GetById($el['PRODUCT_ID'])->GetNext();
	if(!$good) CSaleBasket::Delete($el['ID']);
	$good['QUANTITY'] = floor($el['QUANTITY']);
	$goods[] = $good;
	$price += $el['PRICE'] * $el['QUANTITY'];
	$num += $el['QUANTITY'];
}
if($_POST['delivery']!=''){
	setcookie("delivery",$_POST['delivery'],time()+24*3600*7,"/");
	setcookie("delivery_price",$_POST['delivery_price'],time()+24*3600*7,"/");
	$_COOKIE['delivery'] = $_POST['delivery'];
	$_COOKIE['delivery_price'] = $_POST['delivery_price'];
}
?>

<?

// discount
if(!isset($disc_price)) $disc_price = $price;
if($_SESSION['discount_type'] == 'P'){
	$discount = floor($disc_price * $_SESSION['discount_val'] / 100);
	$disc_price = $disc_price - $discount;
}elseif($_SESSION['discount_type'] == 'F'){
	$discount = $_SESSION['discount_val'];
	if($discount > $disc_price) $discount = $disc_price;
	$disc_price = $disc_price - $discount;
}
$_SESSION['discount_summ'] = $discount;
if($discount > 0) $discount = iarga::prep($discount);
?>



<p><span><span class="icon"><img src="<?=$template?>/images/icon-info-amount.png" alt=""></span><?=GetMessage("IN_BASKET")?> <strong><a href="/basket/" class="innerlink"><?=$num?></a></strong>&nbsp;<?=GetMessage(iarga::sklon($num,"GOODS","GOOD","2GOODS"))?>, <a href="/basket/"><?=iarga::prep($price)?><?=GetMessage("VALUTE_SMALL")?></span></a> 
<a class="bt_gray" href="/basket/"><?=GetMessage("VIEW")?></a></p>
<?foreach($goods as $good):?>
	<input type="hidden" name="<?=$good['ID']?>" value="<?=$good['QUANTITY']?>">
<?endforeach;?>
<input type="hidden" name="allsumm" value="<?=iarga::prep($disc_price+$_COOKIE['delivery_price'])?>">
<input type="hidden" name="allgoods" value="<?=$num?>">
<input type="hidden" name="delivery" value="<?=$_COOKIE['delivery']?>">
<input type="hidden" name="delivery_price" value="<?=$_COOKIE['delivery_price']?>">
<input type="hidden" name="valute" value="<?=GetMessage("VALUTE_MEDIUM")?>">
<input type="hidden" name="discount_value" value="<?if($discount>0):?><?=GetMessage("YOUR_DISCOUNT")?> <?=$discount?><?=GetMessage("VALUTE_SMALL")?><?endif;?>">
