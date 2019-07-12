<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

$frame = $this->createFrame('', false)->begin();
?>

	<div class="informer <?
	?>hidden-xs <?
	?>js-fix <?
	?>js-comparelist <?
	?><?/*=($arResult['COMPARE_CNT'] < 1 ? 'compare_hidden' : '')*/?> <?
	?>compare_hidden <?
	?>js-toggle" <?
	?>data-fix="{'classFixed': 'informer__bar_fix'}" <?
	?>data-toggle="{'classActive': 'compare_hidden', 'onevent': 'click', 'unevent': ''}" <?
	?>><?
	?><div class="informer__bar js-fix-bar">
			<div class="container">
				<div class="col col-xs-9">
					<div class="informer-status js-informer-status">
						<?=Loc::getMessage('CATALOG_COMPARE_PRODUCT')?>&nbsp;<span class="informer-product js-compare-product"></span>
						&nbsp;<span class="hidden informer-status__add js-compare-add"><?=Loc::getMessage('ADDED_IN_COMPARE')?></span>
						<span class="hidden informer-status__del js-compare-del"><?=Loc::getMessage('DELETED_FROM_COMPARE')?></span>
					</div>

					<div class="dropdown informer-dropdown">
						<a class="informer-switcher dropdown-toggle" href="javascript:;" data-toggle="dropdown" aria-expanded="true">
							<?=Loc::getMessage('PRODUCTS_COMPARED')?>&nbsp;<span class="js-comparelist-count"><?=$arResult['COMPARE_CNT']?></span>
							<i class="fa fa-angle-down informer-icon informer-icon_down"></i>
							<i class="fa fa-angle-up informer-icon informer-icon_up"></i>
						</a>

						<div class="dropdown-menu informer-menu js-informer-menu js-favorite_in">
						<?
							include($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/compare_items.php');
						?>
						</div>

					</div>
				</div>

				<a class="pull-right informer-close js-toggle-switcher" href="javascript:;"><?=Loc::getMessage('CLOSE')?></a>
				<a class="pull-right btn btn-default btn2 btn2_mod" href="<?=$arParams["COMPARE_URL"]?>"><?=Loc::getMessage('COMPARE')?></a>
			</div>
		</div>
	</div>

<?$frame->end();?>
