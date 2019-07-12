<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
if (!function_exists("showFilePropertyField"))
{

	function showFilePropertyField($name, $property_fields, $values, $max_file_size_show = 50000)
	{
		$res = "";
		if (!is_array($values) || empty($values))
		{
			$values = array(
				"n0" => 0,
			);
		}

		if ($property_fields["MULTIPLE"] == "N")
		{
			$res = "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[0]\" id=\"" . $name . "[0]\"></label>";
		}
		else
		{
			$res = '
			<script type="text/javascript">
				function addControl(item)
				{
					var current_name = item.id.split("[")[0],
						current_id = item.id.split("[")[1].replace("[", "").replace("]", ""),
						next_id = parseInt(current_id) + 1;

					var newInput = document.createElement("input");
					newInput.type = "file";
					newInput.name = current_name + "[" + next_id + "]";
					newInput.id = current_name + "[" + next_id + "]";
					newInput.onchange = function() { addControl(this); };

					var br = document.createElement("br");
					var br2 = document.createElement("br");

					BX(item.id).parentNode.appendChild(newInput);
				}
			</script>
			';

			$res .= "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[0]\" id=\"" . $name . "[0]\"></label>";
			$res .= "<label for=\"\"><input type=\"file\" size=\"" . $max_file_size_show . "\" value=\"" . $property_fields["VALUE"] . "\" name=\"" . $name . "[1]\" id=\"" . $name . "[1]\" onChange=\"javascript:addControl(this);\"></label>";
		}

		return $res;
	}

}

if (!function_exists('PrintPropsForm'))
{
	function PrintPropsForm($arSource = array(), $locationTemplate = '.default')
	{
		if (empty($arSource))
		{
			return;
		}
		?>
		<?php foreach ($arSource as $arProperties): ?>
			<div class="order-checkout-props-block">
				<?php if ($arProperties['TYPE'] != 'CHECKBOX'): ?>
					<label><?php echo $arProperties['NAME'] ?><?php if ($arProperties['REQUIED_FORMATED'] == 'Y'): ?> <span class="required">*</span><?php endif; ?>:</label>
				<?php endif; ?>
				<?php
				switch ($arProperties['TYPE'])
				{
					case 'TEXT':
						?>
						<input type="text" maxlength="250" size="<?php echo $arProperties['SIZE1'] ?>" value="<?php echo $arProperties['VALUE'] ?>" name="<?php echo $arProperties['FIELD_NAME'] ?>" id="<?php echo $arProperties['FIELD_NAME'] ?>">
						<?php
						break;
					case 'CHECKBOX':
						?>
						<input type="checkbox" name="<?php echo $arProperties['FIELD_NAME'] ?>" value="Y" id="<?php echo $arProperties['FIELD_NAME'] ?> <? if ($arProperties['CHECKED'] == 'Y') echo ' checked'; ?>">;
						<label><?php echo $arProperties['NAME'] ?><?php if ($arProperties['REQUIED_FORMATED'] == 'Y'): ?> <span class="required">*</span><?php endif; ?>:</label>
						<?php
						break;
					case 'SELECT':
					case 'MULTISELECT':
						?>
						<select<?php echo $arProperties['TYPE'] == 'MULTISELECT' ? ' multiple' : '' ?> name="<?php echo $arProperties['FIELD_NAME'] ?>" id="<?php echo $arProperties['FIELD_NAME'] ?>" size="<?php echo $arProperties['SIZE1'] ?>">
							<?php foreach ($arProperties['VARIANTS'] as $arVariants): ?>
								<option value="<?php echo $arVariants['VALUE'] ?>"<?php if ($arVariants['SELECTED'] == 'Y') echo ' selected'; ?>><?php echo $arVariants['NAME'] ?></option>
							<?php endforeach; ?>
						</select>
						<?php
						break;
					case 'TEXTAREA':
						$rows = ($arProperties['SIZE2'] > 10) ? 4 : $arProperties['SIZE2'];
						?>
						<textarea rows="<?php echo $rows ?>" cols="<?php echo $arProperties['SIZE1'] ?>" name="<?php echo $arProperties['FIELD_NAME'] ?>" id="<?php echo $arProperties['FIELD_NAME'] ?>"><?php echo $arProperties['VALUE'] ?></textarea>
						<?php
						break;
					case 'LOCATION':
						$value = 0;
						if (is_array($arProperties['VARIANTS']) && count($arProperties['VARIANTS']) > 0)
						{
							foreach ($arProperties['VARIANTS'] as $arVariant)
							{
								if ($arVariant['SELECTED'] == 'Y')
								{
									$value = $arVariant['ID'];
									break;
								}
							}
						}
						?>
						<?php
						$GLOBALS["APPLICATION"]->IncludeComponent(
							"bitrix:sale.ajax.locations", $locationTemplate, array(
							"AJAX_CALL" => "N",
							"COUNTRY_INPUT_NAME" => "COUNTRY",
							"REGION_INPUT_NAME" => "REGION",
							"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
							"CITY_OUT_LOCATION" => "Y",
							"LOCATION_VALUE" => $value,
							"ORDER_PROPS_ID" => $arProperties["ID"],
							"ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
							"SIZE1" => $arProperties["SIZE1"],
							), null, array('HIDE_ICONS' => 'Y')
						);
						?>
						<?php
						break;
					case 'RADIO':
						?>
						<?php if (is_array($arProperties['VARIANTS'])): ?>
							<?php foreach ($arProperties["VARIANTS"] as $arVariants): ?>
								<input
									type="radio"
									name="<?= $arProperties["FIELD_NAME"] ?>"
									id="<?= $arProperties["FIELD_NAME"] ?>_<?= $arVariants["VALUE"] ?>"
									value="<?= $arVariants["VALUE"] ?>" <? if ($arVariants["CHECKED"] == "Y") echo " checked"; ?> />
								<label for="<?= $arProperties["FIELD_NAME"] ?>_<?= $arVariants["VALUE"] ?>"><?= $arVariants["NAME"] ?></label>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php
						break;
					case 'FILE':
						?>
						<?= showFilePropertyField("ORDER_PROP_" . $arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"]) ?>
						<?php
						break;
				}
				?>
				<?php if (!empty($arProperties['DESCRIPTION'])): ?>
					<div class="order-checkout-props-desc"><?php echo $arProperties['DESCRIPTION'] ?></div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<?
	}
}
?>