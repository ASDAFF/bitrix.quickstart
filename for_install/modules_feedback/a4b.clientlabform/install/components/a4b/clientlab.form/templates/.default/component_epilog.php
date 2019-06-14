<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$form = $arResult['FORM'];
//console_log($form);
?>


<div style="visibility:hidden;">
	<?php foreach ($form as $f => $field): ?>
		<?php if ($field['type'] == "file"): ?>
			<div class="clientlab-form-attachment-field js-cf-attachment-field" data-field-name="<?php echo $field['name'] ?>">

				<?php
					$defaultOptions = array(
						"INPUT_NAME"=> preg_replace( '/[-_]/i', '', $field['name']),
						"MULTIPLE"=>"Y",
						"MODULE_ID"=>"main",
						"MAX_FILE_SIZE"=>"",
						"ALLOW_UPLOAD"=>"A",
						"ALLOW_UPLOAD_EXT"=>""
					);

					$userOptions = array(
						"INPUT_NAME" => preg_replace( '/[-_]/i', '', $field['name']),
						"MULTIPLE" => $field['multiple'],
						"MODULE_ID" => "main",
						"MAX_FILE_SIZE" => "",
						"ALLOW_UPLOAD" => $field['allow_upload'],
						"ALLOW_UPLOAD_EXT" => $field['allow_upload_ext']
					);


					$options = array();

					/* Merge arrays */
					foreach ($defaultOptions as $key => $value) {
						if ($userOptions[$key] != '') {
							$options[$key] = $userOptions[$key];
						}else{
							$options[$key] = $defaultOptions[$key];
						}
					}

				?>

				<?$APPLICATION->IncludeComponent("bitrix:main.file.input", ($field['subtype'] == "file" ? '.default' : "drag_n_drop1"),
					$options,
					false
				);?>
			</div><!-- /.clientlab-form-attachment-field -->
		<?php endif ?>
	<?php endforeach ?>
</div>
