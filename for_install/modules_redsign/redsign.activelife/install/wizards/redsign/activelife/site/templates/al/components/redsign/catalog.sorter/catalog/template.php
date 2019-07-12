<?php

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;


if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();
?>

<nav class="sorter js-catalog_refresh" id="<?=$arParams['TEMPLATE_AJAXID']?>_sorter" data-ajax-id="<?=$arParams['TEMPLATE_AJAXID']?>">
<?php
$this->SetViewTarget($arParams['TEMPLATE_AJAXID'].'_sorter');

$frame = $this->createFrame($arParams['TEMPLATE_AJAXID'].'_sorter', false)->begin();
$frame->setBrowserStorage(true);
?>
	<?php if ('Y' == $arParams['ALFA_SORT_BY_SHOW']): ?>
    <ul class="sorter__order nav-tabs">
        <li class="nav-tabs__name"><?=Loc::getMessage('MSG_SORT')?></li>
        <?php
        $arrUsed = array();
        $arrUsed[] = $arResult['USING']['CSORTING']['ARRAY']['GROUP'];
        ?>
        <?php foreach ($arResult['CSORTING'] as $sort): ?>
            <?php if ($sort['GROUP'] != ''): ?>
            
                <?php if (!in_array($sort['GROUP'], $arrUsed)): ?>
                    <?php
                    $arrUsed[] = $sort['GROUP'];
                    /*
                    $uri = new Uri($sort['URL']);
                    $uri->addParams(
                        array(
                            'ajax_id' => $arParams['TEMPLATE_AJAXID'],
                        )
                    );
                    $sort['URL'] = $uri->getUri();
                    */
                    ?>
                    <li>
                        <a href="<?=$sort['URL']?>">
                            <?=Loc::getMessage('CSORTING_'.$sort['GROUP'])?>
                            <i class="sorter__by <?=$sort['DIRECTION']?>"></i>
                        </a>
                    </li>
                <?php elseif ($arResult['USING']['CSORTING']['ARRAY']['VALUE'] == $sort['VALUE']): ?>
                    <li class="active">
                        <?php
                        /*
                        $uri = new Uri($arResult['USING']['CSORTING']['ARRAY']['URL2']);
                        $uri->addParams(
                            array(
                                'ajax_id' => $arParams['TEMPLATE_AJAXID'],
                            )
                        );
                        $arResult['USING']['CSORTING']['ARRAY']['URL2'] = $uri->getUri();
                        */
                        ?>
                        <a href="<?=$arResult['USING']['CSORTING']['ARRAY']['URL2']?>">
                            <?=Loc::getMessage('CSORTING_'.$arResult['USING']['CSORTING']['ARRAY']['GROUP'])?>
                            <i class="sorter__by <?=$arResult['USING']['CSORTING']['ARRAY']['DIRECTION']?>"></i>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    
    <?php if ('Y' == $arParams['ALFA_OUTPUT_OF_SHOW']): ?>
        <ul class="sorter__limit nav-tabs">
            <li class="nav-tabs__name"><?=Loc::getMessage('MSG_OUTPUT')?></li>
            <li class="active dropdown">
                <?php foreach ($arResult['COUTPUT'] as $output): ?>
                    <?php if ($output['USING'] == 'Y'): ?>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <?=($output['VALUE'] > 1000) ? Loc::getMessage('SHOW_ALL_SORTER') : $output['VALUE']?>
                            <i class="sorter__by desc"></i>
                        </a>
                        <?php break; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <ul class="dropdown-menu">
                <?php foreach ($arResult['COUTPUT'] as $output): ?>
                    <?php
                    /*
                    $uri = new Uri($output['URL']);
                    $uri->addParams(
                        array(
                            'ajax_id' => $arParams['TEMPLATE_AJAXID'],
                        )
                    );
                    $output['URL'] = $uri->getUri();
                    */
                    ?>
                    <li>
                        <a href="<?=htmlspecialcharsbx($output['URL'])?>">
                            <?=($output['VALUE'] > 1000) ? Loc::getMessage('SHOW_ALL_SORTER') : $output['VALUE']?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </li>
        </ul>
    <?php endif; ?>
<?php
    $frame->end();

$this->EndViewTarget();
echo $APPLICATION->GetViewContent($arParams['TEMPLATE_AJAXID'].'_sorter');
?>
</nav>