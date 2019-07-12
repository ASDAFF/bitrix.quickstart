<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); $this->setFrameMode(true);  ?>

<? if ($arResult["FORM_TYPE"] == "login"): ?>
    <div class="bx-system-auth-form">
        <div class="auth-menu before_auth reg-nenu">

            <a data-toggle="modal" href="#authForm" class="authbtn login btn"><?= GetMessage("AUTH_LOGIN_BUTTON") ?></a>
            <?php
            if ($arResult["AUTH_SERVICES"]):
                //deb($arResult["AUTH_SERVICES"]);
                $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons",
                    array(
                        "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                        "SUFFIX" => "form",
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );

            endif;

            ?>
            <? if ($arResult["AUTH_SERVICES"]): ?>
                <?
                $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
                    array(
                        "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                        "AUTH_URL" => $arResult["AUTH_URL"],
                        "POST" => $arResult["POST"],
                        "POPUP" => "Y",
                        "SUFFIX" => "form",
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
                ?>
            <? endif ?>
        </div>
    </div>

    <div style="display: none;" id="authForm" class="modal hide fade autorize" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel2" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 id="myModalLabel2"><?= GetMessage("AUTH_LABEL") ?></h3>
        </div>
        <?php
        // <div id="error_container_auth"></div>
        $arResult["AUTH_URL"] = SITE_DIR . 'auth/ajax/forms.php?login=yes';
        ?>
        <div class="modal-body">
            <form id="auth" name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top"
                  action="<?= $arResult["AUTH_URL"] ?>" autocomplete="on">
                <?php /* <form id="auth" name="system_auth_form71975" method="post" target="_top" action="/auth/ajax/forms.php?login=yes" autocomplete="on">*/ ?>
                <? if ($arResult["BACKURL"] <> ''): ?>
                    <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
                <? endif ?>
                <? foreach ($arResult["POST"] as $key => $value): ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
                <? endforeach ?>
                <input type="hidden" name="AUTH_FORM" value="Y"/>
                <input type="hidden" name="TYPE" value="AUTH"/>
                <?php /* <input type="hidden" name="backurl" value="/">
					<input type="hidden" name="AUTH_FORM" value="Y">
					<input type="hidden" name="TYPE" value="AUTH">
					<input type="hidden" value="" name="social_id">
					<input type="hidden" value="auth" name="form_id">*/
                ?>
                <div id="autorize_inputs_i">
                    <div class="login">
                        <div class="name"><?= GetMessage("EMAIL_LABEL") ?></div>
                        <div class="value">
                            <input type="text" maxlength="50" value="<?= $arResult["USER_LOGIN"] ?>" name="USER_LOGIN"
                                   autocomplete="on">
                        </div>
                    </div>
                    <div class="pass">
                        <div class="name"><?= GetMessage("AUTH_PASSWORD") ?></div>
                        <div class="value">
                            <input type="password" maxlength="50" name="USER_PASSWORD" autocomplete="on">
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="autorize_button" id="autorize_button">
                    <a id="forgot_link" class="forgot-link"><?= GetMessage("AUTH_FORGOT_PASSWORD_2") ?></a>
                    <a id="reg_link" class="reg_link" href="#"><?= GetMessage("AUTH_REGISTER") ?></a>
                </div>
                <div class="sub-div-n">
                    <input type="submit" class="btn" value="<?= GetMessage("ENTER_LABEL") ?>">
                </div>
            </form>
            <div class="clear"></div>
        </div>
    </div>
<?php else:

    $cabinetLink = getCabinetLink();
    /*
    data-original-title="<?= $USER->GetFullName() ?>" data-placement="top"
                  rel="tooltip"
                  */
    ?>
    <div class="bx-system-auth-form">
        <div class="auth-menu before_auth tooltip-demo">
            <form class="bs-docs-example form-inline name" id="logout" action="<?= $arResult["AUTH_URL"] ?>">
                <input type="hidden" name="logout" value="yes" x-webkit-speech="">
            <span class="name_m preview" ><a
                    href="<?= $cabinetLink ?>"><? echo TruncateText($USER->GetFullName(), 15); ?></a></span>
                <input id="logout-l" class="icon-exit" type="submit" value="<?= GetMessage("AUTH_LOGOUT_BUTTON") ?>"
                       name="logout_butt" x-webkit-speech="">
            </form>
        </div>
    </div>
    <script type="text/javascript">$("[rel=tooltip]").tooltip({});</script>
<?php
endif;
?>