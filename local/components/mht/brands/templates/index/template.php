<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    ?>
        <ul class="index-brands">
            <?
                foreach($arResult['BRANDS'] as $group){
                    ?>
                        <li id="<?=WP::getEditElementID(441, $group['id'], $this)?>">
                            <div class="name"><?=$group['name']?></div>
                            <ul class="brand-list">
                                <?
                                    foreach($group['brands'] as $brand){
                                        ?>
                                        <li><a href="<?=$brand['brand-link']?>"><?=$brand['name']?></a></li>
                                        <?
                                    }
                                ?>
                            </ul>
                        </li>
                    <?
                }
            ?>
        </ul>
    <?
?>