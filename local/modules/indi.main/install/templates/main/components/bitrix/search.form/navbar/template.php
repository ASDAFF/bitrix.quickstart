<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<form class="navbar-form navbar-right search-form search-form-navbar" action="<?=$arResult['FORM_ACTION']?>" role="search">
	<div class="input-group">
		<?if($arParams['USE_SUGGEST'] === 'Y'):?>
			<?$APPLICATION->IncludeComponent(
				'bitrix:search.suggest.input',
				'',
				array(
					'NAME' => 'q',
					'VALUE' => '',
					'INPUT_SIZE' => 15,
					'DROPDOWN_SIZE' => 10,
				),
				$component, array('HIDE_ICONS' => 'Y')
			);?>
		<?else:?>
			<input
				class="form-control"
				type="text"
				name="q"
				value=""
				<?=$arParams['USE_PLACEHOLDER'] == 'Y' ? 'placeholder="' . GetMessage('BSF_T_SEARCH_PLACEHOLDER') . '"' : ''?>
				<?=$arParams['USE_REQUIRED'] == 'Y' ? 'required' : ''?>
				maxlength="50"
			/>
		<?endif;?>
		<span class="input-group-btn">
			<button class="btn btn-default" type="submit"><?=GetMessage('BSF_T_SEARCH_BUTTON')?></button>
		</span>
	</div>
</form>