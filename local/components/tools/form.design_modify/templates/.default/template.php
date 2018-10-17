<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?/*?><pre><?print_r($arResult);?></pre><?*/?>
<?/*?><pre><?print_r($arParams);?></pre><?*/?>

<script type="text/javascript">

		function DesignModifyWait(FormTemplate, FormTag, FormParam, FormValue, FormMore) {
			if ( typeof(jQuery) == 'undefined' || typeof(EAFunction) == 'undefined' || typeof(ModifyDesignForm) == 'undefined' ) {
				if ( typeof(jQuery) == 'undefined' ) {
					JQ = document.createElement('script');
					JQ.src = '<?=$arResult["JQUERY"]?>';
					JQ.type = 'text/javascript';
					document.getElementsByTagName('head')[0].appendChild(JQ);
				}
				if ( typeof(EAFunction) == 'undefined' ) {
					EAF = document.createElement('script');
					EAF.src = '<?=$arResult["FUNCTION"]?>';
					EAF.type = 'text/javascript';
					document.getElementsByTagName('head')[0].appendChild(EAF);
				}
				if ( typeof(ModifyDesignForm) == 'undefined' ) {
					MDF = document.createElement('script');
					MDF.src = '<?=$arResult["SCRIPT"]?>';
					MDF.type = 'text/javascript';
					document.getElementsByTagName('head')[0].appendChild(MDF);
				}
				window.setTimeout( function() {
					DesignModifyWait(FormTemplate, FormTag, FormParam, FormValue, FormMore);
				}, 100);
			} else {
				DesignModifyBegin(FormTemplate, FormTag, FormParam, FormValue, FormMore);
			}
		}

	
	function DesignModifyBegin(FormTemplate, FormTag, FormParam, FormValue, FormMore) {
		if ( typeof(jQuery) == 'undefined' || typeof(EAFunction) == 'undefined' || typeof(ModifyDesignForm) == 'undefined' ) {
			DesignModifyWait(FormTemplate, FormTag, FormParam, FormValue, FormMore);
		}else{
			$(document).ready(function(){
				ModifyDesignForm (FormTemplate, FormTag, FormParam, FormValue, FormMore);
			});
		}
	}
	
	var TemplateName = '<?=$arResult["TEMPLATE"]["NAME"]?>';
	
	var EAFormParams = {
			
		/*///params for input type TEXT and PASSWORD///*/
		TextDefaultWidth : '312px',
		TextSmallDefaultWidth : '134px',
		TextBigDefaultWidth : '500px',
		
		TextBefore : '<div class="'+TemplateName+'_EAFormInputText_01"></div><div class="'+TemplateName+'_EAFormInputText_02"></div>',
		TextWrap : [
			/*
			'<div class="wrap1"></div>',
			'<div class="wrap2"></div>'
			*/
		],
		TextAfter : '',
		
		TextDateBefore : '<div class="'+TemplateName+'_EAFormInputText_01"></div><div class="'+TemplateName+'_EAFormInputText_02"></div>',
		TextDateWrap : [
			/*
			'<div class="wrap3"></div>',
			'<div class="wrap4"></div>'
			*/
		],
		TextDateAfter : '',
		
		TextPasswordBefore : '<div class="'+TemplateName+'_EAFormInputText_01"></div><div class="'+TemplateName+'_EAFormInputText_02"></div>',
		TextPasswordWrap : [
			/*
			'<div class="wrap5"></div>',
			'<div class="wrap6"></div>'
			*/
		],
		TextPasswordAfter : '',
			
		
		
		/*///params for input type TEXTAREA///*/
		TextareaDefaultWidth : '312px',
		TextareaDefaultHeight : '129px',
		TextareaSmallDefaultWidth : '134px',
		TextareaSmallDefaultHeight : '67px',
		TextareaBigDefaultWidth : '500px',
		TextareaBigDefaultHeight : '200px',
			
		TextareaBefore : '<div><div class="'+TemplateName+'_EAFormInputTextarea_01"></div><div class="'+TemplateName+'_EAFormInputTextarea_02"></div><div class="'+TemplateName+'_EAFormInputTextarea_03"></div></div>',
		TextareaWrap : [
			'<div class="'+TemplateName+'_EAFormInputTextarea_left"></div>',
			'<div class="'+TemplateName+'_EAFormInputTextarea_right"></div>'
		],
		TextareaAfter : '<div><div class="'+TemplateName+'_EAFormInputTextarea_04"></div><div class="'+TemplateName+'_EAFormInputTextarea_05"></div><div class="'+TemplateName+'_EAFormInputTextarea_06"></div></div>',
		
		
		
		/*///params for input type SELECT MULTIPLE///*/
		SelectMultipleDefaultWidth : '312px',
		SelectMultipleDefaultHeight : '129px',
		SelectMultipleSmallDefaultWidth : '134px',
		SelectMultipleSmallDefaultHeight : '67px',
		SelectMultipleBigDefaultWidth : '500px',
		SelectMultipleBigDefaultHeight : '200px',
		
		SelectMultipleBefore : '<div><div class="'+TemplateName+'_EAFormInputSelectMultiple_01"></div><div class="'+TemplateName+'_EAFormInputSelectMultiple_02"></div><div class="'+TemplateName+'_EAFormInputSelectMultiple_03"></div></div>',
		SelectMultipleWrap : [
			'<div class="'+TemplateName+'_EAFormInputSelectMultiple_left"></div>',
			'<div class="'+TemplateName+'_EAFormInputSelectMultiple_right"></div>'
		],
		SelectMultipleAfter : '<div><div class="'+TemplateName+'_EAFormInputSelectMultiple_04"></div><div class="'+TemplateName+'_EAFormInputSelectMultiple_05"></div><div class="'+TemplateName+'_EAFormInputSelectMultiple_06"></div></div>',
		
		SelectMultipleListItemBefore : '',
		SelectMultipleListItemAfter : '',
			
			
		/*///params for input type SELECT///*/
		SelectDefaultWidth : '312px',
		SelectSmallDefaultWidth : '134px',
		SelectBigDefaultWidth : '500px',
		
		SelectBefore : '<div class="'+TemplateName+'_EAFormInputSelect_01"></div><div class="'+TemplateName+'_EAFormInputSelect_02"></div>',
		SelectWrap : [
			/*
			'<div class="wrap1"></div>',
			'<div class="wrap2"></div>'
			*/
		],
		SelectAfter : '',
		
		SelectBtn : '<div></div>',
			
		SelectPopupDefaultWidth : '312px',
		SelectPopupDefaultHeight : '129px',
		SelectPopupSmallDefaultWidth : '134px',
		SelectPopupSmallDefaultHeight : '67px',
		SelectPopupBigDefaultWidth : '500px',
		SelectPopupBigDefaultHeight : '200px',
		
		SelectPopupBefore : '<div><div class="'+TemplateName+'_EAFormInputSelectPopup_01"></div><div class="'+TemplateName+'_EAFormInputSelectPopup_02"></div><div class="'+TemplateName+'_EAFormInputSelectPopup_03"></div></div>',
		SelectPopupWrap : [
			'<div class="'+TemplateName+'_EAFormInputSelectPopup_left"></div>',
			'<div class="'+TemplateName+'_EAFormInputSelectPopup_right"></div>'
		],
		SelectPopupAfter : '<div><div class="'+TemplateName+'_EAFormInputSelectPopup_04"></div><div class="'+TemplateName+'_EAFormInputSelectPopup_05"></div><div class="'+TemplateName+'_EAFormInputSelectPopup_06"></div></div>',
		
		SelectPopupListItemBefore : '',
		SelectPopupListItemAfter : '',
		
		
		
		/*///params for input type FILE///*/
		FileDefaultWidth : '312px',
		FileSmallDefaultWidth : '134px',
		FileBigDefaultWidth : '500px',
		
		FileBefore : '<div class="'+TemplateName+'_EAFormInputFile_01"></div><div class="'+TemplateName+'_EAFormInputFile_02"></div>',
		FileWrap : [
			/*
			'<div class="wrap1"></div>',
			'<div class="wrap2"></div>'
			*/
		],
		FileAfter : '',
		
		FileBtn : '<div><div class="'+TemplateName+'_EAFormInputFileButton_02"></div><div class="'+TemplateName+'_EAFormInputFileButtonBox"><?=/*$APPLICATION->ConvertCharset(*/GetMessage("FILE_BTN")/*, "UTF-8", "Windows-1251")*/;?></div><div class="'+TemplateName+'_EAFormInputFileButton_01"></div></div>',
			
			
			
		/*///params for input type CHECKED///*/
		CheckboxDefaultWidth : '312px',
		CheckboxSmallDefaultWidth : '134px',
		CheckboxBigDefaultWidth : '500px',
		
		CheckboxBefore : '',
		CheckboxWrap : [
			/*
			'<div class="wrap1"></div>',
			'<div class="wrap2"></div>'
			*/
		],
		CheckboxAfter : '',
		
		
		
		/*///params for input type RADIO///*/
		RadioDefaultWidth : '312px',
		RadioSmallDefaultWidth : '134px',
		RadioBigDefaultWidth : '500px',
		
		RadioBefore : '',
		RadioWrap : [
			/*
			'<div class="wrap1"></div>',
			'<div class="wrap2"></div>'
			*/
		],
		RadioAfter : '',
		
		
		
		/*///params for input type BUTTON///*/
		ButtonDefaultWidth : '312px',
		ButtonSmallDefaultWidth : '134px',
		ButtonBigDefaultWidth : '500px',
		
		ButtonBefore : '<div class="'+TemplateName+'_EAFormInputButton_01"></div><div class="'+TemplateName+'_EAFormInputButton_02"></div>',
		ButtonWrap : [
			/*
			'<div class="wrap1"></div>',
			'<div class="wrap2"></div>'
			*/
		],
		ButtonAfter : '',
		
		
		
		/*///errors message for form///*/
		ValidEmptyErrorMessage : '<?=GetMessage("VALID_EMPTY_ERROR");?>',
		ValidEmailErrorMessage : '<?=GetMessage("VALID_EMAIL_ERROR");?>'
			
	};
	
	$(document).ready(function(){
	<?foreach($arResult["ITEMS"] as $val){?>
		DesignModifyBegin("<?=$arResult["TEMPLATE"]["NAME"]?>", "<?=$val["TAG"]?>", "<?=$val["PARAM"]?>", "<?=$val["VALUE"]?>", "<?=$val["MORE"]?>");
	<?}?>
	});
		
</script>