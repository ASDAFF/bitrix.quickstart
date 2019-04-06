<?
class CWDA_ElementUpdate extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'ELEMENT_UPDATE';
	CONST NAME = 'Пересохранение элементов инфоблоков';
	//
	static function GetDescription() {
		$Descr = 'Плагин выполняет операцию по пересохранению элементов/товаров.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'SELECT_EVENT_CCatalogProduct_Update' => 'Обновить товар торгового каталога [CCatalogProduct::Update]',
			'SELECT_EVENT_CIBlockElement_Update' => 'Обновить элемент инфоблока [CIBlockElement::Update]',
			'ALERT_NOTHING_SELECTED' => 'Выберите хотя бы одно действие для пересохранения',
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
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if($('#wda_settings_<?=self::CODE?> input[type=checkbox]:checked').length==0) {
					alert('<?=self::GetMessage('ALERT_NOTHING_SELECTED',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('SECTION_LINK_TYPE_TITLE');?></div>
			<div>
				<?if(IsModuleInstalled('catalog')):?>
					<div style="margin-bottom:6px;">
						<label>
							<input type="checkbox" id="event_CCatalogProduct_Update" name="params[event_CCatalogProduct_Update]" value="Y" checked="checked" /> <?=self::GetMessage('SELECT_EVENT_CCatalogProduct_Update');?>
						</label>
					</div>
				<?endif?>
				<div>
					<label>
						<input type="checkbox" id="event_CIBlockElement_Update" name="params[event_CIBlockElement_Update]" value="Y" checked="checked" /> <?=self::GetMessage('SELECT_EVENT_CIBlockElement_Update');?>
					</label>
				</div>
			</div>
		</div>
		<?
	}
	static function Process($ElementID, $arElement, $Params) {
		if($Params['event_CCatalogProduct_Update']=='Y' && IsModuleInstalled('catalog')) {
			if (!CCatalogProduct::Update($ElementID,array())) {
				return false;
			}
		}
		if($Params['event_CIBlockElement_Update']=='Y') {
			$IBlockElement = new CIBlockElement;
			$bUpdated = $IBlockElement->Update($ElementID,array(),false,false,false,false);
			unset($IBlockElement);
			if(!$bUpdated) {
				return false;
			}
		}
		return true;
	}
}
?>