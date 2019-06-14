<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?echo '<?xml version="1.0" encoding="'. LANG_CHARSET. '"?>';?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="<?=$arResult["DATE"]?>">
    <shop>
		<name></name>
        <company></company>
        <url><?="http://".$_SERVER["HTTP_HOST"]?></url>
        <currencies>
			<?if ( !empty($arResult["CURRENCIES"]) ):?>
				<?foreach($arResult["CURRENCIES"] as $k=>$cur):?>
					<?if(!empty($cur) && $cur != 'RUR'):?><currency id="<?=$cur?>"<?if ( $cur == 'RUB' ):?> rate="1"<?endif;?>/><?endif;?>
				<?endforeach;?>
			<?else:?>
				<currency id="<?=$arParams["CURRENCY"]?>" rate="1"/>
			<?endif;?>
        </currencies>
		
	<categories>
<?foreach($arResult["CATEGORIES"] as $arCategory):?>
	<?if($arCategory['OFFERS'])continue;?>
		<category id="<?=$arCategory["ID"]?>"<?
if($arCategory["PARENT"])
	echo ' parentId="'. $arCategory['PARENT']. '"';
?>><?=$arCategory["NAME"]?></category>
<?endforeach;?>
	</categories>	
	<?if($arParams["LOCAL_DELIVERY_COST"]):?>
	<local_delivery_cost><?=$arParams["LOCAL_DELIVERY_COST"]?></local_delivery_cost>
	<?endif?>
        <offers>
        <?foreach($arResult["OFFER"] as $arOffer):?>
			<offer id="<?=$arOffer["ID"]?>" available="<?=$arOffer["AVALIABLE"]?>">
				<url><?=$arOffer["URL"]?></url>
				<price><?=$arOffer["PRICE"]?></price>
				
				<currencyId>
					<?if ( !empty($arOffer["CURRENCY"]) ):?>
						<?=$arOffer["CURRENCY"]?>
					<?else:?>
						<?=$arParams["CURRENCY"]?>
					<?endif;?>
				</currencyId>
				
				<categoryId><?=$arOffer["CATEGORY"]?></categoryId>
				<?if($arOffer["PICTURE"]):?><picture><?=$arOffer["PICTURE"]?></picture><?endif?>
				<?if($arOffer['MORE_PHOTO']):?>
					<?foreach($arOffer['MORE_PHOTO'] as $pic):?>
						<picture><?=$pic?></picture>
					<?endforeach?>
				<?endif?>
				<name><?=$arOffer["MODEL"]?></name>
				<?if($arOffer["DESCRIPTION"]):?><description><?=$arOffer["DESCRIPTION"]?></description><?endif?>
				
				<?if(is_array($arOffer['PROPERTIES'])):?>
					<?foreach($arOffer['PROPERTIES'] as $key => $val):?>
						<param name="<?=$val['NAME']?>"><?=($val['VALUE_E'])?$val['VALUE_E']:$val['VALUE']?></param>
					<?endforeach;?>				
				<?endif;?>
				<?if(is_array($arOffer['SKU'])):?>
					<?foreach($arOffer['SKU'] as $key => $sku):?>
						<?$price = $sku['PRICE'];?>
						<?if (is_array($sku['SKU_PROP'])) :?>
							<?foreach($sku['SKU_PROP'] as $k => $v):?>
								<param name="<?=$v['NAME']?>" price="<?=$price?>"><?=$v['VALUE']?></param>
							<?endforeach?>
						<?endif;?>
					<?endforeach?>
				<?endif;?>
			</offer>
        <?endforeach;?>
        </offers>
    </shop>
</yml_catalog>