<?
$dbBasketItems = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
        false,
        false,
        array("PRODUCT_ID")
    );
while ($arItems = $dbBasketItems->Fetch())
{
	$db_props = CIBlockElement::GetProperty("1", $arItems["PRODUCT_ID"], array("sort" => "asc"), Array("CODE"=>"accessories"));
	while($ar_props = $db_props->GetNext())
	$arAccessoriesItems[] .= IntVal($ar_props["VALUE"]);
}
foreach($arAccessoriesItems as $newarAccessoriesItems ){
$arrAccessoriesItems = array_merge_recursive($arrAccessoriesItems,$newarAccessoriesItems);
}?> 				
<div class="b-sidebar-filter m-sidebar"> 					
  <div class="b-tab-head"> 						<a href="#" class="b-tab-head__link active" >Аксессуары</a> 					</div>
 					<button class="b-slider-vert__btn m-vert__up"></button> 					
  <div class="b-slider-vert"> 						
    <ul class="b-slider-vert__list"> <?
$arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL","PROPERTY_type_VALUE");
$arFilter = Array("IBLOCK_ID"=>"1","ID"=>$arAccessoriesItems, "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();
  ?> 
      <li class="b-slider-vert__item"> 		
						
        <div class="b-slider-vert__image"><img src="<?=($arFields['PREVIEW_PICTURE']?CFile::GetPath($arFields['PREVIEW_PICTURE']):"/images/img-element__image.png");?>" alt="<?=$arFields['NAME']?>"  /></div>
       								
        <div class="b-slider-vert__link"><?=$arFields['PROPERTY_TYPE_VALUE_VALUE']?> <a href="<?=$arFields['DETAIL_PAGE_URL']?>" ><?=$arFields['NAME']?></a></div>
       								
        <div class="b-slider__price"> 								<?
								$ar_res = CPrice::GetBasePrice($arFields['ID']);
								echo CurrencyFormat($ar_res["PRICE"], $ar_res["CURRENCY"]);
								?> 								</div>
       								
        <div><a href="?action=ADD2BASKET&id=<?=$arFields['ID']?>" id="<?=$arFields['ID']?>" class="b-icon m-icon__buy" rel="nofollow" title="В корзину" ></a></div>
       </li>
     <?  
}
?> </ul>
   					</div>
 					<button class="b-slider-vert__btn m-vert__down"></button> 				</div>
