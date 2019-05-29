<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

					<div class="window-form">
                        <a href="<?php echo $arParams['_SITE_DIR']; ?>" class="close-win"><?php echo GetMessage("LS_CS_CLOSE"); ?></a>

                        <div class="window-confirm">
                            <img src="<?php echo($parentTemplateFolder); ?>/img/ok-icon2.png" alt="confirm" class="icon">

                            <h2 class="grey"><?php echo GetMessage("LS_CS_CONFIRM_NOTE_1"); ?></h2>
                            <div class="text"><?php echo GetMessage("LS_CS_CONFIRM_NOTE_2"); ?></div>
                            <div class="button">
                                <a href="<?php echo $arParams['_SITE_DIR']; ?>" class="button-grey"><?php echo GetMessage("LS_CS_CONFIRM_OK"); ?></a>
                            </div>
                        </div>
                    </div>