<?
class CWD_Popup {
	function Init($Force=false) {
		if ($Force || (defined('WD_POPUP_NEED_INCLUDE') && WD_POPUP_NEED_INCLUDE===true) || COption::GetOptionString('webdebug.popup','webdebug_popup_always_init')=='Y') {
			global $APPLICATION;
			$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/webdebug.popup.css"/>',true);
			$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/webdebug.popup/webdebug.popup.js"></script>',true);
		}
	}
	function Animations() {
		return array('fadeAndPop','fade','none');
	}
	function Link($ID=false, $AppendToBody=false, $Animation=false, $Close=true, $CallbackOpen=false, $CallbackShow=false, $CallbackClose=false, $Autoopen=false, $AutoopenDelay=500) {
		if (!$ID) $ID = 'default';
		if ($AppendToBody) {
			$AppendToBody = ' data-reveal-tobody="Y"';
		} else {
			$AppendToBody = '';
		}
		if (in_array($Animation,self::Animations())) {
			$Animation = ' data-animation="'.$Animation.'"';
		} else {
			$Animation = '';
		}
		if ($CallbackOpen) {
			$CallbackOpen = ' data-callback-open="'.$CallbackOpen.'"';
		} else {
			$CallbackOpen = '';
		}
		if ($CallbackShow) {
			$CallbackShow = ' data-callback-show="'.$CallbackShow.'"';
		} else {
			$CallbackShow = '';
		}
		if ($CallbackClose) {
			$CallbackClose = ' data-callback-close="'.$CallbackClose.'"';
		} else {
			$CallbackClose = '';
		}
		if ($Autoopen) {
			if (!is_numeric($AutoopenDelay) || $AutoopenDelay<0) $AutoopenDelay = 500;
			$Autoopen = ' data-autoopen="Y" data-autoopen-delay="'.$AutoopenDelay.'"';
		} else {
			$Autoopen = '';
		}
		$Close = ' data-close="popup_'.$ID.'_close"'.(!$Close?' data-no-close="Y"':'');
		$strResult = ' data-reveal-id="popup_'.$ID.'_window"'.$Close.' data-overlay="popup_'.$ID.'_overlay"'.$Animation.$AppendToBody.$CallbackOpen.$CallbackShow.$CallbackClose.$Autoopen;
		return $strResult;
	}
	function Begin($ID=false, $Title=false, $Width=false, $arClasses=array(), $Close=true, $CallbackInit=false, $DisplayNone=false) {
		$GLOBALS['WD_POPUPS_NEED_INCLUDE'] = true;
		$strResult = '';
		$Style = '';
		$DataDisplayNone = '';
		if ($DisplayNone) {
			$Style .= ' display:none;';
			$DataDisplayNone = ' data-display="none"';
		}
		if (IntVal($Width)>0) {
			$Style .= ' margin-left:-'.IntVal($Width/2).'px; width:'.IntVal($Width).'px;';
		}
		if ($CallbackInit) {
			$CallbackInit = ' data-callback-init="'.$CallbackInit.'"';
		} else {
			$CallbackInit = '';
		}
		$Classes = '';
		if (!is_array($arClasses) && trim($arClasses)!='') {
			$Classes = ' '.trim($arClasses);
		} elseif (is_array($arClasses) && !empty($arClasses)) {
			$Classes = ' '.implode(' ', $arClasses);
		}
		$Style = trim($Style);
		if ($Style!='') $Style = ' style="'.$Style.'"';
		$strResult .= '<div id="popup_'.$ID.'_window" class="wd_popup_window'.$Classes.'"'.$Style.$CallbackInit.$DataDisplayNone.'>'."\n";
		$strResult .= "\t".'<a class="wd_popup_close" id="popup_'.$ID.'_close" title=""'.(!$Close?' style="display:none"':'').'>&times;</a>'."\n";
		$strResult .= "\t".'<div class="wd_popup_title">'.($Title?$Title:'').'</div>'."\n";
		$strResult .= "\t".'<div class="wd_popup_inner wd_popup_content">'."\n";
		return $strResult;
	}
	function End() {
		$strResult = '';
		$strResult .= "\t".'</div>'."\n";
		$strResult .= '</div>'."\n";
		return $strResult;
	}
	function LinkEx(&$arParams) {
		return self::Link(
			$arParams['ID'],
			$arParams['APPEND_TO_BODY']=='N'?false:true,
			(in_array($arParams['ANIMATION'],self::Animations())?$arParams['ANIMATION']:false),
			$arParams['CLOSE']=='N'?false:true,
			$arParams['CALLBACK_OPEN'],
			$arParams['CALLBACK_SHOW'],
			$arParams['CALLBACK_CLOSE'],
			$arParams['POPUP_AUTOOPEN'],
			$arParams['POPUP_AUTOOPEN_DELAY']
		);
	}
	function BeginEx($arParams) {
		if ($arParams['LINK_TO']) {
			self::JSAddLinkTo($arParams);
		}
		return self::Begin(
			$arParams['ID'],
			(trim($arParams['NAME'])==''?false:$arParams['NAME']),
			(IntVal($arParams['WIDTH'])<0?false:IntVal($arParams['WIDTH'])),
			$arParams['CLASSES'],
			$arParams['CLOSE']=='N'?false:true,
			$arParams['CALLBACK_INIT'],
			$arParams['DISPLAY_NONE']=='Y'?true:false
		);
	}
	function EndEx() {
		return self::End();
	}
	function LinkToJSON($Data) {
		preg_match_all('#([A-z0-9-_]+)="(.*?)"#i',$Data,$M);
		$arResult = array();
		foreach($M[0] as $Key => $Value) {
			$arResult[] = '"'.$M[1][$Key].'":"'.$M[2][$Key].'"';
		}
		$strResult .= '{'.implode(",",$arResult).'}';
		return $strResult;
	}
	function JSAddLinkTo($arParams) {
		?>
		<script>
			if (window.WD_Popup_LinkTo==undefined) window.WD_Popup_LinkTo = [];
			var WD_Popup_JSON = <?=self::LinkToJSON(self::LinkEx($arParams));?>;
			window.WD_Popup_LinkTo["#wd_func_opener_<?=$arParams['ID']?>, <?=$arParams['LINK_TO']?>"] = WD_Popup_JSON;
			var PopupFuncOpener = $('<input type="button" id="wd_func_opener_<?=$arParams['ID']?>" value="" />').appendTo($('body').eq(0));
		</script>
		<?
	}
}
?>