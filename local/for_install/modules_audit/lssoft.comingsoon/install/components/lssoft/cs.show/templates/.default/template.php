<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

                    <div class="right">
                        <div class="share">
                            <?php if (isset($arParams['~LIKE'])) echo($arParams['~LIKE']); ?>
                        </div>
                    </div>

                    <div class="left">
                        <h2><?php if (isset($arParams['TITLE'])) echo($arParams['TITLE']); ?></h2>
                        <div class="text"><?php if (isset($arParams['DESCRIPTION'])) echo($arParams['DESCRIPTION']); ?></div>

						<?php if (isset($arParams['INVITE_ENABLED']) and $arParams['INVITE_ENABLED']=='Y') { ?>
                        <div class="button">
                            <a href="<?php echo($APPLICATION->GetCurPage()); ?>?CSP=registration" class="button-yellow"><?php echo GetMessage("LS_CS_GET_INVITE"); ?></a>
                        </div>
                        <?php } ?>
                    </div>