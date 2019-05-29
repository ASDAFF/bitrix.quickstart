<?php
IncludeModuleLangFile(__FILE__);

$types = $profileUtils->GetTypes();

$yandex_market = array(
    "ym_simple",
    "ym_vendormodel",
    "ym_book",
    "ym_audiobook",
    "ym_multimedia",
    "ym_tour",
    "ym_clothes"
);

$yandex_realty = array(
    "y_realty"
);

$yandex_webmaster = array(

);

$google = array(
    "google"
);

$wikimart = array(
	"wikimart_clothes",
	"wikimart_vendormodel",
);

$tiu = array(
	"tiu_simple",
	"tiu_vendormodel"
);

$mailru = array(
	"mailru",
	"mailru_clothing"
);

$allbiz = array(
	"allbiz",
);

$activizm = array(
	"activizm"
);

$avito = array(
    "avito_realty",
    "avito_avto",
	"avito_furniture",
	"avito_context",
);

$ebay = array(
	"ebay_1",
	"ebay_2",
	"ebay_mp30",
);

$ozon = array(
	"ozon",
);

$pulscen = array(
	"pulscen",
);

$lengow = array(
    "lengow",
);


$advantshop = array(
	"advantshop",
);

$price_ru = array(
    "price_ru",
);

$ua_nadavi_net = array(
    "ua_nadavi_net",
);

$ua_hotline_ua = array(
    "ua_hotline_ua",
);

$ua_technoportal_ua = array(
    "ua_technoportal_ua",
);

$ua_price_ua = array(
    "ua_price_ua",
);

$ua_prom_ua = array(
    "ua_prom_ua",
);
?>

<tr class="heading" align="center">
	<td colspan="2">
		<b><?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE")?></b>
	</td>
</tr>

<tr>
	<td>
        <span id="hint_PROFILE[TYPE]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[TYPE]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTTYPE_LABEL_HELP" )?>' );</script>
        <?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_LABEL")?>
    </td>
	<td> 
		<select name="PROFILE[TYPE]">
            <? $selected = $arProfile["TYPE"] == "optional" ? 'selected="selected"' : ""; ?>
            <option value="optional" <?=$selected?>><?=$types["optional"]["NAME"]?></option>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_YANDEX")?>">
                <optgroup label="&nbsp;&nbsp;&nbsp;<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_YANDEX_MARKET")?>">
                    <?foreach($yandex_market as $typeCode):?>
                        <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                        <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                    <?endforeach?>
                </optgroup>
                <optgroup label="&nbsp;&nbsp;&nbsp;<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTTYPE_YANDEX_REALTY" )?>">
                    <?foreach( $yandex_realty as $typeCode ):?>
                        <?$selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                        <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                    <?endforeach;?>
                </optgroup>
            </optgroup>
            
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_GOOGLE")?>">
				 <?foreach($google as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_WIKIMART")?>">
				 <?foreach($wikimart as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_TIU")?>">
				 <?foreach($tiu as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_MAIL.RU")?>">
				 <?foreach($mailru as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_ALLBIZ")?>">
				 <?foreach($allbiz as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_ACTIVIZM")?>">
				 <?foreach($activizm as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_AVITO")?>">
				 <?foreach($avito as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_EBAY")?>">
				 <?foreach($ebay as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_OZON")?>">
				 <?foreach($ozon as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_PULSCEN")?>">
				 <?foreach($pulscen as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
			<optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_LENGOW")?>">
				 <?foreach($lengow as $typeCode):?>
					 <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
					 <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
				 <?endforeach?>
			</optgroup>
            <optgroup label="<?=GetMessage("ACRIT_EXPORTPRO_EXPORTTYPE_ADVANTSHOP")?>">
                 <?foreach( $advantshop as $typeCode ):?>
                     <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                 <?endforeach?>
            </optgroup>
            <optgroup label="<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTTYPE_PRICE_RU" )?>">
                 <?foreach( $price_ru as $typeCode ):?>
                     <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                 <?endforeach?>
            </optgroup>
            <optgroup label="<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTTYPE_UA_NADAVI_NET" )?>">
                 <?foreach( $ua_nadavi_net as $typeCode ):?>
                     <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                 <?endforeach?>
            </optgroup>
            <optgroup label="<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTTYPE_UA_HOTLINE_UA" )?>">
                 <?foreach( $ua_hotline_ua as $typeCode ):?>
                     <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                 <?endforeach?>
            </optgroup>
            <optgroup label="<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTTYPE_UA_TECHNOPORTAL_UA" )?>">
                 <?foreach( $ua_technoportal_ua as $typeCode ):?>
                     <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                 <?endforeach?>
            </optgroup>
            <optgroup label="<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTTYPE_UA_PRICE_UA" )?>">
                 <?foreach( $ua_price_ua as $typeCode ):?>
                     <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                 <?endforeach?>
            </optgroup>
            <optgroup label="<?=GetMessage( "ACRIT_EXPORTPRO_EXPORTTYPE_UA_PROM_UA" )?>">
                 <?foreach( $ua_prom_ua as $typeCode ):?>
                     <? $selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : ""; ?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                 <?endforeach?>
            </optgroup>
		</select>
	</td>
</tr>
<tr class="heading"><td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_EXPORT_REQUIREMENTS" );?></td></tr>
<tr>
    <td colspan="2" id="portal_requirements" style="text-align: center;">
        <a href="<?=$types[$arProfile["TYPE"]]["PORTAL_REQUIREMENTS"];?>" target="_blank"><?=$types[$arProfile["TYPE"]]["PORTAL_REQUIREMENTS"];?></a>
    </td>
</tr>
<tr class="heading"><td colspan="2"><?=GetMessage("ACRIT_EXPORTPRO_EXPORT_EXAMPLE")?></td></tr>
<tr>
	<td colspan="2" style="background:#FDF6E3" id="description">
		<?
			if($siteEncoding[SITE_CHARSET] != "utf8")
				echo "<pre>",  htmlspecialchars($types[$arProfile["TYPE"]]["EXAMPLE"], ENT_COMPAT | ENT_HTML401, $siteEncoding[SITE_CHARSET]), "</pre>";
			else
				echo "<pre>",  htmlspecialchars($types[$arProfile["TYPE"]]["EXAMPLE"]), "</pre>";
		?>
	</td>
</tr>

