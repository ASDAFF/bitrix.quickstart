<?
class CWDA_ProductSet extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'PRODUCT_SET';
	CONST NAME = 'Создание наборов/комплектов';
	//
	static function GetDescription() {
		$Descr = 'Плагин позволяет собирать наборы и комплекты для выбранных по фильтру товаров.<br/>Если в результате обработки имеются ошибки, смотрите файл лога.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'SET_TYPE' => 'Набор или комплект:',
			'SET_TYPE_2' => 'набор',
			'SET_TYPE_1' => 'комплект',
			//
			'HEADER_PRODUCT' => 'Товар',
			'HEADER_QUANTITY' => 'Кол-во',
			'HEADER_PERCENT' => 'Процент',
			'HEADER_SORT' => 'Сорт.',
			'HEADER_DELETE' => 'Удал.',
			//
			'ADD_ROW' => 'Добавить товар',
			//
			'EMPTY_MOTICE' => 'Добавьте как минимум один товар.',
			//
			'ALERT_EMPTY_PRODUCTS' => 'Необходимо добавить как минимум один товар в набор/комплект.',
			'ALERT_PERCENT_TOO_MUCH' => 'Сумма процентов не должна превышать 100.',
			'ALERT_QUANTITY_EMPTY' => 'Необходимо указать количества для всех товаров в наборе/комплекте.',
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
		BX.addCustomEvent('onWdaAfterActionChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				// Check products count
				var FilledProducts = 0;
				$('#wda_product_set_products table tbody tr:visible').each(function(){
					var ID = parseInt($(this).find('td.product input[type=text]').val());
					if(!isNaN(ID) && ID>0) {
						FilledProducts++;
					}
				});
				if(FilledProducts==0) {
					alert('<?=self::GetMessage('ALERT_EMPTY_PRODUCTS',true);?>');
					WdaCanSubmit = false;
				}
				// Check quantity
				if(WdaCanSubmit!=false) {
					$('#wda_product_set_products table tbody td.product:visible input[type=text]').each(function(){
						if($(this).val().length>0 && $.trim($(this).closest('tr').find('td.quantity input[type=text]').val()).length==0) {
							alert('<?=self::GetMessage('ALERT_QUANTITY_EMPTY',true);?>');
							WdaCanSubmit = false;
						}
					});
				}
				// Check percent
				if(WdaCanSubmit!=false) {
					var IsBundle = $('#wda_product_set_type input[type=radio][value=1]').is(':checked');
					if (IsBundle) {
						var Percent = 0;
						$('#wda_product_set_products table tbody td.percent:visible input[type=text]').each(function(){
							var Value = parseInt($(this).val());
							if(isNaN(Value) || Value<0) {
								Value = 0;
							}
							Percent += Value;
						});
						if(Percent>100) {
							alert('<?=self::GetMessage('ALERT_PERCENT_TOO_MUCH',true);?>');
							WdaCanSubmit = false;
						}
					}
				}
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			var IBlock = $('#wda_select_iblock').val();
			// Target
			var SelectTarget = $('#wda_price_target').html(Select.html());
			SelectTarget.find('optgroup').not('optgroup[data-group=PRICES]').remove();
			SelectTarget.change();
		}
		function WDA_<?=self::CODE?>_RecalculateCount(){
			var Count = $('#wda_product_set_products tbody tr').length;
			$('#wda_product_set_products').attr('data-count',Count);
		}
		$(document).delegate('#wda_product_set_type input[type=radio]','change',function(){
			var CheckedRadio = $('#wda_product_set_type input[type=radio]:checked');
			if(CheckedRadio.length==1) {
				$('#wda_product_set_products').removeClass('type_1 type_2').addClass('type_'+$(this).val());
			}
		}).delegate('#wda_product_set_product_add','click',function(){
			var TBody = $('#wda_product_set_products tbody').first();
			var TR = TBody.children('tr').first();
			var TR_New = TR.clone().css('display','');
			TBody.append(TR_New);
			WDA_<?=self::CODE?>_RecalculateCount();
		}).delegate('#wda_product_set_products tbody td.delete input','click',function(){
			var TR = $(this).closest('tr');
			TR.remove();
			WDA_<?=self::CODE?>_RecalculateCount();
		});
		//
		function SelectProductPopupCallback(Product, IBlockID){
			var Caller = $(window.SelectProductPopupCaller);
			if(Caller.length==1) {
				var TD = Caller.closest('td');
				TD.find('input[type=text]').val(Product.id);
				TD.find('.product_name').html(Product.name);
			}
			window.SelectProductPopup.Close();
		}
		function ShowSelectProductPopup(Caller){
			window.SelectProductPopup = new BX.CDialog({
				content_url: '/bitrix/admin/cat_product_search_dialog.php?lang=<?=LANGUAGE_ID;?>&func_name=SelectProductPopupCallback',
				height: Math.max(500, window.innerHeight-400),
				width: Math.max(800, window.innerWidth-400),
				draggable: true,
				resizable: true,
				min_height: 500,
				min_width: 800
			});
			BX.addCustomEvent(window.SelectProductPopup, 'onWindowRegister', BX.defer(function(){
				window.SelectProductPopup.Get().style.position = 'fixed';
				window.SelectProductPopup.Get().style.top = (parseInt(window.SelectProductPopup.Get().style.top) - BX.GetWindowScrollPos().scrollTop) + 'px';
			}));
			window.SelectProductPopupCaller = Caller;
			window.SelectProductPopup.Show();
		}
		</script>
		<style>
		#wda_product_set_type input[type=radio] {margin:0 0 0 5px; vertical-align:middle;}
		#wda_product_set_type span {vertical-align:middle;}
		#wda_product_set_products th {min-width:100px;}
		#wda_product_set_products.type_2 .percent {display:none;}
		#wda_product_set_products .empty_notice {color:dimgray; display:none; font-size:12px; font-style:italic; padding:10px;}
		#wda_product_set_products[data-count="0"] .empty_notice {display:block; }
		#wda_product_set_products tbody td.delete {width:36px;}
		#wda_product_set_products tbody td input[type=text] {width:100%; -moz-box-sizing:border-box; -webkit-box-sizing:border-box; box-sizing:border-box;}
		#wda_product_set_products tbody td .product_name {color:dimgray; font-size:12px; font-style:italic; margin-top:4px; text-align:left;}
		#wda_product_set_products tbody td .product_flex {display:flex;}
		#wda_product_set_products tbody td .product_flex input[type=text] {margin-right:10px;}
		#wda_product_set_products tbody td .product_flex input[type=button] {margin-top:-2px;}
		</style>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div id="wda_product_set_type">
				<?=self::GetMessage('SET_TYPE');?>
				<label><input type="radio" name="params[set_type]" value="2" checked="checked" /> <span><?=self::GetMessage('SET_TYPE_2');?></span></label>
				<label><input type="radio" name="params[set_type]" value="1" /> <span><?=self::GetMessage('SET_TYPE_1');?></span></label>
			</div>
			<br/>
			<div id="wda_product_set_products" class="type_2" data-count=0>
				<div class="products">
					<div class="adm-list-table-wrap">
						<table class="adm-list-table">
							<thead>
								<tr class="adm-list-table-header">
									<td class="adm-list-table-cell product"><div class="adm-list-table-cell-inner"><?=self::GetMessage('HEADER_PRODUCT');?></div></td>
									<td class="adm-list-table-cell quantity"><div class="adm-list-table-cell-inner"><?=self::GetMessage('HEADER_QUANTITY');?></div></td>
									<td class="adm-list-table-cell sort"><div class="adm-list-table-cell-inner"><?=self::GetMessage('HEADER_SORT');?></div></td>
									<td class="adm-list-table-cell percent"><div class="adm-list-table-cell-inner"><?=self::GetMessage('HEADER_PERCENT');?></div></td>
									<td class="adm-list-table-cell delete"><div class="adm-list-table-cell-inner"><?=self::GetMessage('HEADER_DELETE');?></div></td>
								</tr>
							</thead>
							<tbody>
								<tr class="adm-list-table-row" style="display:none">
									<td class="adm-list-table-cell align-right product">
										<div class="product_flex">
											<input type="text" name="params[products][id][]" value="" size="10" maxlength="10" />
											<input type="button" value="..." class="select_product" onclick="ShowSelectProductPopup(this);" />
										</div>
										<div class="product_name"></div>
									</td>
									<td class="adm-list-table-cell align-right quantity"><input type="text" name="params[products][quantity][]" value="1" size="10" maxlength="10" /></td>
									<td class="adm-list-table-cell align-right sort"><input type="text" name="params[products][sort][]" value="100" size="10" maxlength="10" /></td>
									<td class="adm-list-table-cell align-right percent"><input type="text" name="params[products][percent][]" value="" size="10" maxlength="3" /></td>
									<td class="adm-list-table-cell align-right delete"><input type="button" value="&times;" /></td>
								</tr>
							</tbody>
						</table>
						<div class="empty_notice"><?=self::GetMessage('EMPTY_NOTICE');?></div>
					</div>
				</div>
				<br/>
				<input type="button" value="<?=self::GetMessage('ADD_ROW');?>" id="wda_product_set_product_add" />
			</div>
		</div>
		<?
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		if(CModule::IncludeModule('catalog') && class_exists('CCatalogProductSet') && is_array($Params['products']) && !empty($Params['products'])) {
			$intType = $Params['set_type']==1 ? CCatalogProductSet::TYPE_SET : CCatalogProductSet::TYPE_GROUP;
			//
			$arSetItems = array();
			foreach($Params['products']['id'] as $ProductIndex => $ID){
				$ID = IntVal($ID);
				if($ID===0) {
					continue;
				}
				$arSetItem = array(
					'ACTIVE' => 'Y',
					'ITEM_ID' => $ID,
					'QUANTITY' => $Params['products']['quantity'][$ProductIndex],
					'SORT' => $Params['products']['sort'][$ProductIndex],
				);
				if($intType==CCatalogProductSet::TYPE_SET) {
					$arSetItem['DISCOUNT_PERCENT'] = $Params['products']['percent'][$ProductIndex];
				}
				$arSetItems[] = $arSetItem;
			}
			$arSetFields = array(
				'TYPE' => $intType,
				'SET_ID' => 0,
				'ITEM_ID' => $ElementID,
				'ITEMS' => $arSetItems,
			);
			//
			$arProductSets = CCatalogProductSet::getAllSetsByProduct($ElementID, $intType);
			//
			$obCatalogProductSet = new CCatalogProductSet;
			//
			if(is_array($arProductSets) && !empty($arProductSets)) {
				$arProductSets = array_shift($arProductSets);
				unset($arSetFields['SET_ID'], $arSetFields['ITEM_ID'], $arSetFields['TYPE']);
				if($obCatalogProductSet->Update($arProductSets['SET_ID'], $arSetFields)) {
					$obCatalogProductSet->RecalculateSetsByProduct($ElementID);
					$bResult = true;
				} else {
					CWDA::Log('Errors for element #'.$ElementID.' (update mode)');
					CWDA::Log($obCatalogProductSet->getErrors());
				}
			} else {
				$arSetFields['ACTIVE'] = 'Y';
				$obCatalogProductSet = new CCatalogProductSet;
				if ($obCatalogProductSet->Add($arSetFields)) {
					$bResult = true;
				} else {
					CWDA::Log('Errors for element #'.$ElementID.' (add mode)');
					CWDA::Log($obCatalogProductSet->getErrors());
				}
			}
		}
		return $bResult;
	}
}
?>