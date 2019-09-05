<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$templateName = __DIR__ . '/template-' . $arResult['STEP'] . '.php';
?>

<div class="sale-order-ajax sale-order-ajax-default">
	<ul class="nav nav-pills nav-steps">
		<?
		foreach ($arResult['STEPS'] as $stepNum => $step) {
			$last = $stepNum == count($arResult['STEPS']) - 1;
			?>
			<li class="nav-step nav-step-<?=$step?><?=$step == $arResult['STEP'] ? ' active' : ' inactive'?>">
				<a>
					<?=GetMessage('SOA_STEP_' . strtoupper($step))?>
				</a>
			</li>
			<?
		}
		?>
	</ul>
	
	<?
	try {
		if (is_file($templateName)) {
			require $templateName;
		} else {
			throw new Exception(sprintf('Template "%s" isn\'t exists.', $arResult['STEP']));
		}
	} catch (Exception $e) {
		ShowError($e->GetMessage());
	}
	?>
</div>