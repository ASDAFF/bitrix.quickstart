<?
class CWDA_LinkSection extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'LINK_SECTION';
	CONST NAME = 'Привязки к разделам';
	//
	static function GetDescription() {
		$Descr = 'Плагин выполняет операции по переносу элементов в заданный раздел, а также по добавлению привязки к заданному разделу.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'SECTION_LINK_TYPE_TITLE' => 'Выберите тип операции:',
				'LINK_TYPE_SET' => 'Перенести в раздел',
				'LINK_TYPE_ADD' => 'Добавить привязку к разделу',
				'LINK_TYPE_DEL' => 'Удалить привязку к разделу',
			'SECTION_TITLE' => 'Выберите раздел:',
				'SELECT_SECTION_EMPTY' => '--- выберите раздел ---',
		);
		$MESS = trim($MESS[$Code]);
		if ($ConvertCharset && !CWDA::IsUtf()) {
			$MESS = CWDA::ConvertCharset($MESS);
		}
		return $MESS;
	}
	//
	static function AddHeadData() {
		?>
		<script>
		BX.addCustomEvent('onWdaAfterIBlockChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaAfterActionChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if($('#wda_select_link_section').val().length==0) {
					WdaCanSubmit = false;
				}
			}
		});
		function WDA_<?=self::CODE?>_Fill(){
			$('#wda_select_link_section').html($('#wda_select_sections').html()).find('option[value=""]').text('<?=self::GetMessage('SELECT_SECTION_EMPTY',true);?>').addClass('empty');
		}
		</script>
		<style>
		#wda_select_link_section {min-height:250px; min-width:500px;}
		</style>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('SECTION_LINK_TYPE_TITLE');?></div>
			<div>
				<select name="params[section_link_type]" id="wda_select_section_link_type">
					<option value="set"><?=self::GetMessage('LINK_TYPE_SET');?></option>
					<option value="add"><?=self::GetMessage('LINK_TYPE_ADD');?></option>
					<option value="del"><?=self::GetMessage('LINK_TYPE_DEL');?></option>
				</select>
			</div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('SECTION_TITLE');?></div>
			<div>
				<select name="params[link_section]" id="wda_select_link_section" size="12"></select>
			</div>
		</div>
		<?
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		$NewSectionID = IntVal($Params['link_section']);
		if($NewSectionID>0) {
			$IBlockElement = new CIBlockElement;
			switch($Params['section_link_type']) {
				case 'set':
					$IBlockElement = new CIBlockElement;
					$bResult = $IBlockElement->Update($ElementID,array('IBLOCK_SECTION_ID'=>$NewSectionID));
					break;
				case 'add':
					$resSections = CIBlockElement::GetElementGroups($ElementID, true, array('ID'));
					while ($arSection = $resSections->GetNext(false,false)) {
						$arSectionsID[] = $arSection['ID'];
					}
					$arSectionsID[] = $NewSectionID;
					$arSectionsID = array_unique($arSectionsID);
					$bResult = CIBlockElement::SetElementSection($ElementID,$arSectionsID);
					break;
				case 'del':
					$arSectionsID = array();
					$resSections = CIBlockElement::GetElementGroups($ElementID, true, array('ID'));
					while ($arSection = $resSections->GetNext(false,false)) {
						if($arSection['ID']==$NewSectionID) {
							continue;
						}
						$arSectionsID[] = $arSection['ID'];
					}
					$bResult = CIBlockElement::SetElementSection($ElementID,$arSectionsID);
					break;
			}
		}
		if($bResult){
			$IBlockElement->Update($ElementID,array('WD'=>'Y'));
		}
		return $bResult;
	}
}
?>