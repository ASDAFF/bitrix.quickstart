<?if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!empty($arParams["TEMPLATE_AJAX_ID"])):
  ?><div id="<?=$arParams['TEMPLATE_AJAX_ID']?>_sorter"><?
endif;

$this->SetViewTarget($arParams['TEMPLATE_AJAX_ID'].'_sorter');

?><div class="catalogsorter <?
		?>clearfix <?
		?><?=$arParams["USE_AJAX"]=="Y" ? "js-sorterajax" : ""?>"<?
    ?>id="composite_sorter"<?
    ?>data-catalog-template="<?=$arParams["TEMPLATE_AJAX_ID"]?>"<?
		?><?if (isset($arParams['TEMPLATE_AJAX_ID']) && $arParams['TEMPLATE_AJAX_ID']!=''):?> data-ajaxpagesid="<?=$arParams['TEMPLATE_AJAX_ID']?>"<?endif;?>><?
	$frame = $this->createFrame('composite_sorter', false)->begin();
	$frame->setBrowserStorage(true);

	if ($arParams['USE_FILTER'] == 'Y'):
		?><div class="hidden pull-left filterbtn dropdown">
			<button class="btn btn-default dropdown-toggle showfilter" type="button">
				<i class="fa"></i>
			</button>
		</div><?
	endif;

	if ($arParams['ALFA_OUTPUT_OF_SHOW'] == 'Y'):
		?><div class="hidden-xs loss-menu-right">
			<span class="title"><?=Loc::getMessage('RS.FLYAWAY.OUTPUT_TITLE')?></span>
			<div class="dropdown js-sorter">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuOutput" data-toggle="dropdown" aria-expanded="true">
					<i class="fa fa-file-o visible-xs-inline"></i>
					<span class="js-sorter-btn"><?=$arResult['USING']['COUTPUT']['ARRAY']['VALUE']?></span>
					<i class="fa fa-angle-down hidden-xs icon-angle-down"></i>
					<i class="fa fa-angle-up hidden-xs icon-angle-up"></i>
				</button>
				<ul class="dropdown-menu list-unstyled views-box drop-panel" role="menu" aria-labelledby="dropdownMenuOutput">
					<?foreach ($arResult['COUTPUT'] as $output):?>
						<li class="views-item js-sorter-item">
							<a class="js-sorter-switcher" href="<?=$output['URL']?>">
								<span class="js-sorter-text"><?=$output['VALUE']?></span>
							</a>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div><?
	endif;

	if ($arParams['ALFA_SORT_BY_SHOW'] == 'Y'):
        $usingSortName = '';
        if (strpos(strtolower($arResult['USING']['CSORTING']['ARRAY']['VALUE']), 'price') === false) {
            $usingSortName = strtolower($arResult['USING']['CSORTING']['ARRAY']['VALUE']);
        } elseif ($arResult['USING']['CSORTING']['ARRAY']['DIRECTION'] == 'asc') {
            $usingSortName = 'property_price_false_asc';
        } else {
            $usingSortName = 'property_price_false_desc';
        }
		?><div class="loss-menu-right">
			<div class="dropdown dropdown_wide js-sorter">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuSortBy" data-toggle="dropdown" aria-expanded="true">
					<span class="visible-xs"><?=Loc::getMessage('RS.FLYAWAY.SORT')?></span>
					<span class="hidden-xs">
						<span class="js-sorter-btn"><?=Loc::getMessage('RS.FLYAWAY.SORT_VARIABLE.'.$usingSortName)?></span>
						<i class="fa fa-angle-down hidden-xs icon-angle-down"></i>
						<i class="fa fa-angle-up hidden-xs icon-angle-up"></i>
					</span>
					<span class="hidden">
						<?=Loc::getMessage('RS.FLYAWAY.SORT_VARIABLE_ICON.'.$usingSortName)?>
					</span>
				</button>
				<ul class="dropdown-menu list-unstyled views-box drop-panel" role="menu" aria-labelledby="dropdownMenuSortBy">

					<?foreach ($arResult['CSORTING'] as $key => $sort):
                        if(strtolower($sort['VALUE']) == 'sort_desc') continue;
                        $sortName = '';
                        if (strpos(strtolower($sort['VALUE']), 'price') === false) {
                            $sortName = strtolower($sort['VALUE']);
                        } elseif ($sort['DIRECTION'] == 'asc') {
                            $sortName = 'property_price_false_asc';
                        } else {
                            $sortName = 'property_price_false_desc';
                        }
                    ?>
					<li class="views-item js-sorter-item<?=($arResult['USING']['CSORTING']['KEY'] == $key ? ' views-item_current' : '')?>">
						<a class="js-sorter-switcher" href="<?=$sort['URL']?>">
							<span class="js-sorter-text"><?=Loc::getMessage('RS.FLYAWAY.SORT_VARIABLE.'.$sortName)?></span>
							<span class="hidden"><?=Loc::getMessage('RS.FLYAWAY.SORT_VARIABLE_ICON.'.$sortName)?></span>
						</a>
					</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
	<?endif;?>

	<div class="pull-right">
		<?if ($arParams['ALFA_CHOSE_TEMPLATES_SHOW'] == 'Y'):?>
			<div class="pull-right template views-products js-views" data-views="{'classActive' : 'active'}">
				<?foreach ($arResult['CTEMPLATE'] as $key => $template):
					$key = $key + 1;
					?><div class="loss-menu-right<?=($template['USING'] == 'Y' ? ' active' : '')?> js-views-switcher" data-index="<?=$key?>"><?
						?><a class="selected js-sorterajax-switcher" href="<?=$template['URL']?>" data-fvalue="<?=CUtil::JSEscape($template['VALUE'])?>" title="<?=($template['NAME_LANG']!=''?$template['NAME_LANG']:$template['VALUE'])?>">
							<i class="fa <?=$template['VALUE']?>"></i>
						</a><?
					?></div><?
				endforeach;?>
			</div>

			<div class="hidden pull-right templateDrop dropdown">
				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuTemplate" data-toggle="dropdown" aria-expanded="true">
					<i class="fa <?=$template['VALUE']?>"></i>
				</button>
				<ul class="dropdown-menu list-unstyled" role="menu" aria-labelledby="dropdownMenuTemplate">
					<?foreach ($arResult['CTEMPLATE'] as $template):?>
						<li>
							<a href="<?=$template['URL']?>">
								<i class="fa <?=$template['VALUE']?>"></i>
							</a>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		<?endif;?>

		<div class="comparising pull-right js-comparising"></div>
	</div>

	<?$frame->end();?>
	</div>

<script>
	if ($('.js-comparising-list').length > 0) {
		$('.js-comparising').append($('.js-comparising-list').clone());
	}
</script>

<?
$this->EndViewTarget();
?>

<?
echo $APPLICATION->GetViewContent($arParams['TEMPLATE_AJAX_ID'].'_sorter');

if (!empty($arParams["TEMPLATE_AJAX_ID"])):
  ?></div><?
endif;
