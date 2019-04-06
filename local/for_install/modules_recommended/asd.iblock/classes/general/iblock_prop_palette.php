<?php
IncludeModuleLangFile(__FILE__);

define ('ASD_UT_PALETTE', 'SASDPalette');

class CASDiblockPropPalette {
	public static function GetUserTypeDescription() {
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => ASD_UT_PALETTE,
			'DESCRIPTION' => GetMessage('ASD_UT_PALETTE_DESCR'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
		);
	}

	public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName) {
		$strID = preg_replace('/[^a-zA-Z0-9_]/i', 'x', $strHTMLControlName['VALUE']);
		if (array_key_exists('MODE', $strHTMLControlName) && ($strHTMLControlName['MODE'] == 'iblock_element_admin')) {
			$strResult = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strID.'" value="'.htmlspecialcharsbx($arValue['VALUE']).'" />';
		} else {
			CJSCore::Init(array('asd_palette'));
			$strResult = '<div style="position: relative;"><input type="text" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strID.'" value="'.htmlspecialcharsbx($arValue['VALUE']).'" />';
			$strResult .= '<script type="text/javascript">
				BX.ready(function()
    			{
      				$("#'.$strID.'").jPicker({
      					window: {
title: "'.GetMessage('ASD_UT_PALETTE_WND_TITLE').'",
position: {x: \'screenCenter\', y: '.(
					defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ?
					'screen.height / 2 -150' :
					'\'bottom\''
				).'}
},
      					images: {clientPath : "/bitrix/js/asd.iblock/jpicker/images/"},
      					localization: {
							text: {
								title: "'.GetMessage('ASD_UT_PALETTE_WND_TITLE').'",
								newColor: "'.GetMessage('ASD_UT_PALETTE_WND_NEW_COLOR').'", currentColor: "'.GetMessage('ASD_UT_PALETTE_WND_CURRENT_COLOR').'",
								ok: "'.GetMessage('ASD_UT_PALETTE_WND_OK').'", cancel: "'.GetMessage('ASD_UT_PALETTE_WND_CANCEL').'"
    						},
							tooltips: {
								colors: { newColor: "'.GetMessage('ASD_UT_PALETTE_TIPS_NEW_COLOR').'", currentColor: "'.GetMessage('ASD_UT_PALETTE_TIPS_CURRENT_COLOR').'" },
								buttons: { ok: "'.GetMessage('ASD_UT_PALETTE_TIPS_BTN_OK').'", cancel: "'.GetMessage('ASD_UT_PALETTE_TIPS_BTN_CANCEL').'" },
								hue: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_HUE_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_HUE_VALUE').'" },
								saturation: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_SATURATION_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_SATURATION_VALUE').'" },
								value: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_BRIGHTNESS_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_BRIGHTNESS_VALUE').'" },
								red: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_RED_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_RED_VALUE').'" },
								green: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_GREEN_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_GREEN_VALUE').'" },
								blue: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_BLUE_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_BLUE_VALUE').'" },
								alpha: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_ALPHA_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_ALPHA_VALUE').'" },
								hex: { textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_HEX_VALUE').'", alpha: "'.GetMessage('ASD_UT_PALETTE_TIPS_HEX_ALPHA').'" }
							}
						}
					});
    			});
				</script></div>';
		}
		return $strResult;
	}

	public static function GetPublicEditHtml($arProperty, $arValue, $strHTMLControlName) {
		$strID = preg_replace('/[^a-zA-Z0-9_]/i', 'x', $strHTMLControlName["VALUE"]);
		CJSCore::Init(array('asd_palette'));
		$strResult = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.htmlspecialcharsbx($strID).'" value="'.htmlspecialcharsbx($arValue['VALUE']).'" />';
		$strResult .= '<script type="text/javascript">
			BX.ready(function()
   			{
     			$("#'.htmlspecialcharsbx($strID).'").jPicker({
     				window: {title: "'.GetMessage('ASD_UT_PALETTE_WND_TITLE').'"},
     				images: {clientPath : "/bitrix/js/asd.iblock/jpicker/images/"},
      				localization: {
						text: {
							title: "'.GetMessage('ASD_UT_PALETTE_WND_TITLE').'",
							newColor: "'.GetMessage('ASD_UT_PALETTE_WND_NEW_COLOR').'", currentColor: "'.GetMessage('ASD_UT_PALETTE_WND_CURRENT_COLOR').'",
							ok: "'.GetMessage('ASD_UT_PALETTE_WND_OK').'", cancel: "'.GetMessage('ASD_UT_PALETTE_WND_CANCEL').'"
    					},
						tooltips: {
							colors: { newColor: "'.GetMessage('ASD_UT_PALETTE_TIPS_NEW_COLOR').'", currentColor: "'.GetMessage('ASD_UT_PALETTE_TIPS_CURRENT_COLOR').'" },
							buttons: { ok: "'.GetMessage('ASD_UT_PALETTE_TIPS_BTN_OK').'", cancel: "'.GetMessage('ASD_UT_PALETTE_TIPS_BTN_CANCEL').'" },
							hue: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_HUE_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_HUE_VALUE').'" },
							saturation: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_SATURATION_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_SATURATION_VALUE').'" },
							value: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_BRIGHTNESS_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_BRIGHTNESS_VALUE').'" },
							red: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_RED_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_RED_VALUE').'" },
							green: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_GREEN_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_GREEN_VALUE').'" },
							blue: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_BLUE_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_BLUE_VALUE').'" },
							alpha: { radio: "'.GetMessage('ASD_UT_PALETTE_TIPS_ALPHA_MODE').'", textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_ALPHA_VALUE').'" },
							hex: { textbox: "'.GetMessage('ASD_UT_PALETTE_TIPS_HEX_VALUE').'", alpha: "'.GetMessage('ASD_UT_PALETTE_TIPS_HEX_ALPHA').'" }
						}
					}
     			});
   			});
			</script>';
		return $strResult;
	}
}