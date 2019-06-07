<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * Bitrix vars
 *
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @var array $arParams
 * @var array $arResult
 * @var array $arLangMessages
 * @var array $templateData
 *
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $parentTemplateFolder
 * @var string $templateName
 * @var string $componentPath
 *
 * @var CDatabase $DB
 * @var CUser $USER
 * @var CMain $APPLICATION
 */
?>
<script type="text/javascript">

	function BeginPrint()
	{
		var data_string = $('#print_form').serialize();
		var WinPrint;

		WinPrint = open('','printWindow','left=50,top=50,width=800,height=600,status=0,location=0,menubar=1,toolbar=1,scrollbars=1,resizable=1,fullscreen=0');
		WinPrint.location = location.protocol + '//' + location.hostname + '<?=$arParams["PRINT_FILE_URL"];?>?' + data_string;
		WinPrint.focus();

		return false;
	}

	function BeginFile(ext)
	{
		var data_string = $('#print_form').serialize();
		if(ext.length)
		{
			window.location.href = data_string + '&FILE_MODE='+ext;
		}
		else
			alert('<?=GetMessage('SET_FILE_EXTENSION');?>');

		return false;
	}

</script>
<form name="print_form" id="print_form" action="<?=$APPLICATION->GetCurPage();?>" method="post" class="ts-print-form">
	<input type="hidden" name="set_print" value="Y" />
<!--	<input type="hidden" name="IB" value="--><?//=$arParams['IBLOCK_ID'];?><!--" />-->
	<input type="hidden" name="C_A_S" value="<?=$arParams['CHECK_ACTIVE_SECTION'];?>" />
	<div class="ts-date-field">
		<input type="text" value="<?=date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")),mktime (0,0,0,date("m"), date("d") - 7, date("Y")));?>" name="D_A_F_L" id="D_A_F_L" placeholder="<?=GetMessage('OT');?>">
		<?$APPLICATION->IncludeComponent(
			'bitrix:main.calendar',
			'',
			array(
			     'FORM_NAME' => "print_form",
			     'INPUT_NAME' => "D_A_F_L",
			     'INPUT_VALUE' => '',
			     'HIDE_TIMEBAR' => 'Y',
			),
			null,
			array('HIDE_ICONS' => 'Y')
		);
		?>
	</div>
	<div class="ts-date-field">
		<input type="text" value="<?=date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), time());?>" name="D_A_F_R" id="D_A_F_R" placeholder="<?=GetMessage('DO');?>">
		<?$APPLICATION->IncludeComponent(
			'bitrix:main.calendar',
			'',
			array(
			     'FORM_NAME' => 'print_form',
			     'INPUT_NAME' => "D_A_F_R",
			     'INPUT_VALUE' => '',
			     'HIDE_TIMEBAR' => 'Y',
			),
			null,
			array('HIDE_ICONS' => 'Y')
		);
		?>
	</div>
	<div class="ts-buttons">
		<?if(!empty($arParams['TEXT_TEMPLATE'])):?>
			<input type="hidden" name="TEXT_TEMPLATE" value="<?=base64_encode($arParams['TEXT_TEMPLATE']);?>" />
		<?endif;?>
		<?if(!empty($arParams['CSS_FILE_URL'])):?>
			<input type="hidden" name="CSS_FILE_URL" value="<?=base64_encode($arParams['CSS_FILE_URL']);?>" />
		<?endif;?>
		<?if(!empty($arParams["FIELD_CODE"])):?>
			<input type="hidden" name="FIELD_CODE" value="<?=base64_encode(serialize($arParams["FIELD_CODE"]));?>" />
		<?endif;?>
		<?if(!empty($arParams["PROPERTY_CODE"])):?>
			<input type="hidden" name="PROPERTY_CODE" value="<?=base64_encode(serialize($arParams["PROPERTY_CODE"]));?>" />
		<?endif;?>
		<?if($arParams['PPS']):?>
			<input type="hidden" name="PPS" value="<?=base64_encode(serialize($arParams["PPS"]));?>" />
		<?endif;?>
		<?if($arParams['DPS']):?>
			<input type="hidden" name="DPS" value="<?=base64_encode(serialize($arParams["DPS"]));?>" />
		<?endif;?>
		<button type="button" name="button_print" class="ts-print-btn" onclick="BeginPrint(); return false;" value="<?=GetMessage('GO_PRINT');?>">
			<img src="<?=$templateFolder;?>/images/print.png" alt="print.png" /> <?=GetMessage('GO_PRINT');?>
		</button>
		<?if($arParams['ENABLE_PDF']):?>
		&nbsp;&nbsp;<button class="ts-icon-pdf" type="submit" name="FILE_MODE" value="pdf"><img src="<?=$templateFolder;?>/images/ext_pdf.png" alt="pdf" /> <span><?=GetMessage('PDF');?></span></button>
		<?endif;?>
	</div>
</form>