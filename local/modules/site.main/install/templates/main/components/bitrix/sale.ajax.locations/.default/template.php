<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * Отображает селектор списка местоположений
 *
 * @param string $name Название селектора
 * @param string $value Выбранное значение
 * @param array $locations Список значений
 * @param string $defaultText Значение для не выбранного варианта
 * @param string $cssClass СSS класс
 * @param boolean $disabled Деактивирован
 * @param callback $locationRenderer Обработчик вывода местоположения
 * @return void
 */
$printLocations = function($name, $value, $locations, $defaultText = '', $cssClass = '', $disabled = false, $locationRenderer = null) {
	if (count($locations) == 0) {
		return;
	}
	?>
	<div class="form-group">
		<select class="form-control <?=$cssClass?>" name="<?=$name?>"<?=$disabled ? ' disabled=""' : ''?>>
			<?
			if ($defaultText) {
				?><option value=""><?=$defaultText?></option><?
			}
			
			foreach ($locations as $location) {
				$selected = $location['ID'] == $value;
				?>
				<option value="<?=$location['ID']?>" <?=$selected ? ' selected=""' : ''?>>
					<?=$locationRenderer ? $locationRenderer($location) : $location['NAME_LANG']?>
				</option>
				<?
			}
			?>
		</select>
	</div>
	<?
};

?>
<div class="sale-ajax-locations sale-ajax-locations-default" data-ajax-gate="<?=$templateFolder?>/ajax.php" data-params='<?=json_encode($arParams)?>'>
	<?if ($arParams['AJAX_CALL'] != 'Y') {
		?><script src="<?=$templateFolder?>/template.js"></script><?
	}
	
	$disabled = false;
	if (count($arParams['LOC_DEFAULT']) > 0
		&& $arParams['PUBLIC'] != 'N'
		&& $arParams['SHOW_QUICK_CHOOSE'] == 'Y'
	) {
		if (isset($_REQUEST['NEW_LOCATION_' . $arParams['ORDER_PROPS_ID']]) 
			&& IntVal($_REQUEST['NEW_LOCATION_' . $arParams['ORDER_PROPS_ID']]) > 0
		) {
			$disabled = true;
		}
		$checked = false;
		
		?>
		<div class="locations-default">
			<?
			foreach ($arParams['LOC_DEFAULT'] as $val) {
				if (($val['ID'] == IntVal($_REQUEST['NEW_LOCATION_' . $arParams['ORDER_PROPS_ID']]) || $val['ID'] == $arParams['CITY'])
					 && (!isset($_REQUEST['CHANGE_ZIP']) || $_REQUEST['CHANGE_ZIP'] != 'Y')
				) {
					$checked = true;
					$disabled = true;
				}
				?>
				<div class="form-group">
					<div class="radio">
						<label>
							<input
								type="radio"
								name="NEW_LOCATION_<?=$arParams['ORDER_PROPS_ID']?>"
								value="<?=$val['ID']?>"
								<?=$checked ? ' checked=""' : ''?>
							/>
							<?=$val['LOC_DEFAULT_NAME']?>
						</label>
					</div>
				</div>
				<?
			}?>
			
			<div class="form-group">
				<div class="radio">
					<label>
						<input
							type="radio"
							name="NEW_LOCATION_<?=$arParams['ORDER_PROPS_ID']?>"
							value="0"
							<?=$checked ? '' : ' checked=""'?>
						/>
						<?=GetMessage('LOC_DEFAULT_NAME_NULL')?>
					</label>
				</div>
			</div>
		</div>
		<?
	}
	?>
	
	<div class="location-select row">
		<div class="col-md-4">
			<?
			$printLocations(
				$arParams['COUNTRY_INPUT_NAME'] . $arParams['CITY_INPUT_NAME'],
				$arParams['COUNTRY'],
				$arResult['COUNTRY_LIST'],
				GetMessage('SAL_CHOOSE_COUNTRY'),
				'location-observable location-country',
				$disabled
			);
			?>
		</div>
		<div class="col-md-4">
			<?
			$printLocations(
				$arParams['REGION_INPUT_NAME'] . $arParams['CITY_INPUT_NAME'],
				$arParams['REGION'],
				$arResult['REGION_LIST'],
				GetMessage('SAL_CHOOSE_REGION'),
				'location-observable location-region',
				$disabled
			);
			?>
		</div>
		<div class="col-md-4">
			<?
			$printLocations(
				$arParams['CITY_INPUT_NAME'],
				$arParams['CITY'],
				$arResult['CITY_LIST'],
				GetMessage('SAL_CHOOSE_CITY'),
				'location-id',
				$disabled,
				function($location) {
					return $location['CITY_ID'] > 0 ? $location['CITY_NAME'] : GetMessage('SAL_CHOOSE_CITY_OTHER');
				}
			);
			?>
		</div>
	</div>
</div>