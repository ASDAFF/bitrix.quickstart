<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();$this->setFrameMode(true);

IncludeTemplateLangFile(__FILE__);

//deb($arResult);
if (count($arResult) < 1)
    return;

//$arParams["IS_MOBILE"] = "N";
?>

<script>
    $(document).ready(function() {

        <?
        if ($arParams["IS_MOBILE"] == "Y") {
            ?>
            // fade effect when click on menu
            $('#menu-top .dropdown-toggle').click(function() {

                 var curMenu = $(this).next('.dropdown-menu');
                 var curTabID = curMenu.data("tab-id");

                 $('#menu-top .dropdown-toggle').each(function() {

                     var objMenu = $(this).next('.dropdown-menu');
                     var curMenuTabID = objMenu.data("tab-id");

                     if (curMenuTabID != curTabID && objMenu.css('display') !== 'none') {
                        objMenu.fadeToggle(250);
                     }
                 });
                 curMenu.fadeToggle(250);
             });
            <?
        } else {
            ?>
            $('#menu-top li.dropdown').hover(function() {
                $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn(250);
            }, function() {
                $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeOut(250);
            });
            <?
        }
        ?>
    });
</script>
<ul class="nav nav-pills menu-top" id="menu-top">
    <?php
    $tabIndex = 0;
    foreach ($arResult as $arItem):
        if (count($arItem["CHILDS"]) > 0):
            $tabIndex++;
            //data-toggle="dropdown"
            ?>
            <li class="root dropdown <? if ($arItem["SELECTED"]) echo "active"; ?>">
                <a class="dropdown-toggle"  role="button" tabindex="<?=$tabIndex?>" href="<?=$arItem['LINK']?>"> <?= $arItem['TEXT'] ?><b
                        class="caret"></b></a>
                <ul class="dropdown-menu my-dr-menu" data-tab-id="<?=$tabIndex?>">
                    <li class="sb-check">
						    <span class="checks">
                                 <span class="l"><a href="<?=SITE_DIR?>brands/"><?=GetMessage("NOVAGR_JWSHOP_POISK_PO_BRENDAM")?> <?=GetMessage("NOVAGR_JWSHOP_A_A")?></a></span>
							    <span class="clear"></span>
							</span>
                    </li>
                    <li>
                        <?php
                        // count how many empty cells need to out in akksessuars
                        $countChilds = count($arItem["CHILDS"]);
                        
                        if ($countChilds>4) {
                        	
                        	$countTd = $countChilds % 4;
                        	$countTd = (4-$countTd);
                        	
                        }
                        ?>
                        <table class="drop-my-menu">
                            <tbody>
                            <tr>
                                <?php
                                $KEY_1 = -1;
                                $tdCounter=1;
                                foreach ($arItem["CHILDS"] as $CHILD_2):
                                    $KEY_1++;
                                    if ($KEY_1 % 4 == 0 and $KEY_1 > 1) print "</tr><tr>";
                                    ?>
                                    <td>
                                        <ul>
                                            <li>
                                                <?php
                                                if ($CHILD_2["IS_PARENT"] != 1) {
                                                    ?><a href="<?=$CHILD_2['LINK']?>" class="parent"><?=$CHILD_2['TEXT']?></a>
                                                <?
                                                } else {
                                                    ?><span class="parent"><?=$CHILD_2['TEXT']?></span>
                                                <?
                                                }

                                                if (count($CHILD_2['CHILDS']) > 0 and is_array($CHILD_2['CHILDS'])):
                                                    ?>
                                                    <ul>
                                                        <?php
                                                        foreach ($CHILD_2['CHILDS'] as $CHILD_3):
                                                            ?>
                                                            <li>
                                                                <a href="<?= $CHILD_3['LINK'] ?>"><?= $CHILD_3['TEXT'] ?></a>
                                                            </li>
                                                        <?php
                                                        endforeach;
                                                        ?>
                                                    </ul>
                                                <?php
                                                endif;
                                                ?>
                                            </li>
                                        </ul>
                                    </td>
                                <?php
                                	if ($tdCounter == $countChilds && $countTd>0) {
                                		echo str_repeat("<td>&nbsp;</td>", $countTd);
                                	}
                                	$tdCounter++;
                                endforeach;
                                if(is_numeric($arItem["PARAMS"]["SECTION"]["UF_PICTURE_MENU"])):
                                ?>
                                <td class="adv-img">
                                    <? echo CFile::ShowImage($arItem["PARAMS"]["SECTION"]["UF_PICTURE_MENU"], 300, 300, "", $arItem["PARAMS"]["SECTION"]["UF_PICTURE_MENU_URL"], false);?>
                                </td>
                                <?endif;?>
                            </tr>
                            </tbody>
                        </table>
                    </li>
                </ul>
            </li>
        <?php else:
            ?>
            <li class="root <? if ($arItem["SELECTED"]) echo "active"; ?>">
                <a class="root-item" href="<?= $arItem['LINK'] ?>"><?= $arItem['TEXT'] ?></a>
            </li>
        <?php
        endif;
    endforeach;
    ?>
</ul>
