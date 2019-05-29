<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

					<div class="window-form">
                        <a href="<?php echo $arParams['_SITE_DIR']; ?>" class="close-win"><?php echo GetMessage("LS_CS_CLOSE"); ?></a>

                        <h2 class="grey"><?php echo GetMessage("LS_CS_REGISTRATION"); ?></h2>
                        
						<?php if ($component->sError) { ?> 
							<span class="note-error"><i></i><?php echo($component->sError); ?></span>
						<?php } ?>

                        <form class="form-soon" action="<?php echo($APPLICATION->GetCurUri()); ?>" method="POST">
                            <p <?php if ($component->sErrorMail) { ?> class="error" <?php } ?>>
                            	<input type="text" class="input-text" name="mail" value="<?php echo(htmlspecialchars($component->sMail)); ?>" placeholder="<?php echo GetMessage("LS_CS_FORM_MAIL"); ?>">
                            	<?php if ($component->sErrorMail) { ?>
                            		<span class="note-error"><i></i><?php echo($component->sErrorMail); ?></span>
                            	<?php } ?>
                            </p>
							<?php if (isset($arParams['INVITE_NEED_LOGIN']) and $arParams['INVITE_NEED_LOGIN']=='Y') { ?>
                            <p <?php if ($component->sErrorLogin) { ?> class="error" <?php } ?>>
                            	<input type="text" class="input-text" name="login" value="<?php echo(htmlspecialchars($component->sLogin)); ?>" placeholder="<?php echo GetMessage("LS_CS_FORM_LOGIN"); ?>">
                            	<?php if ($component->sErrorLogin) { ?>
                            		<span class="note-error"><i></i><?php echo($component->sErrorLogin); ?></span>
                            	<?php } ?>
                            </p>
							<?php } ?>
                            <p class="none-mg">
                            	<input type="submit" value="<?php echo GetMessage("LS_CS_FORM_SUBMIT"); ?>" class="button-simple" name="submit">
                            </p>
                        </form>
                    </div>