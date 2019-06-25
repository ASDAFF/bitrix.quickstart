<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-auth"><div class="login_form_under">
    <?if($arResult["FORM_TYPE"] == "login"):?>
        <a class="startshop-link startshop-link-standart startshop-auth-login" onClick="return openAuthorizePopup();" title="<?=GetMessage("STARTSHOP_AUTH_LOGIN")?>">
            <?=GetMessage("STARTSHOP_AUTH_LOGIN")?>
        </a>
        <script>
            function openAuthorizePopup() {
                if(window.innerWidth < 790) {
                    document.location.href = "/auth";
                }else{
                    var authPopup = BX.PopupWindowManager.create("AuthorizePopup", null, {
                        autoHide: true,
                        offsetLeft: 0,
                        offsetTop: 0,
                        overlay : true,
                        draggable: {restrict:true},
                        closeByEsc: true,
                        closeIcon: { right : "32px", top : "23px"},
                        content: '<div style="width:702px; height:303px; text-align: center;"><span style="position:absolute;left:50%; top:50%"></span></div>',
                        events: {
                            onAfterPopupShow: function() {
                                BX.ajax.post(
                                    '<?=$this->GetFolder().'/ajax/authorize.php'?>',
                                    {
                                        backurl: '<?=htmlspecialchars_decode($arResult["BACKURL"])?>',
                                        sAuthUrl: '<?=htmlspecialchars_decode($arResult["AUTH_URL"])?>',
                                        sForgotPasswordUrl: '<?=htmlspecialchars_decode($arResult["AUTH_FORGOT_PASSWORD_URL"])?>',
                                        sRegisterUrl: '<?=htmlspecialchars_decode($arResult["AUTH_REGISTER_URL"])?>',
                                        SITE_ID: '<?=SITE_ID?>'
                                    },
                                    BX.delegate(function(result)
                                        {
                                            this.setContent(result);
                                        },
                                        this)
                                );
                            }
                        }
                    });
                    authPopup.show();
                }
            }
        </script>
    <?else:?>
        <form action="<?=$arResult["AUTH_URL"]?>" method="POST">
            <?$frame = $this->createFrame()->begin();?>
                <a class="startshop-link startshop-link-standart startshop-auth-profile" href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("STARTSHOP_AUTH_PROFILE")?>">
                    <?=$arResult["USER_NAME"]?>
                </a>
                <?foreach ($arResult["GET"] as $key => $value):?>
                    <input type="hidden" name="<?=htmlspecialcharsbx($key)?>" value="<?=htmlspecialcharsbx($value)?>" />
                <?endforeach?>
                <input type="hidden" name="logout" value="yes" />
                <input class="startshop-link startshop-link-standart startshop-auth-logout" type="submit" name="logout_butt" value="<?=GetMessage("STARTSHOP_AUTH_LOGOUT")?>" />
            <?$frame->beginStub();?>
                <a class="startshop-link startshop-link-standart startshop-auth-login" onClick="return openAuthorizePopup();" title="<?=GetMessage("STARTSHOP_AUTH_LOGIN")?>">
                    <?=GetMessage("STARTSHOP_AUTH_LOGIN")?>
                </a>
            <?$frame->end();?>
        </form>
    <?endif;?>
</div></div>
