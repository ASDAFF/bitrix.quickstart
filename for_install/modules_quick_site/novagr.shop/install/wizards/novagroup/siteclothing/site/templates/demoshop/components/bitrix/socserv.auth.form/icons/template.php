<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();$this->setFrameMode(true);

$cssClasses = array(
    'openid' => 'openid',
    'yandex' => 'yandex',
    'openid-mail-ru' => 'mail',
    'livejournal' => 'journal',
    'liveinternet' => 'liin',
    'blogger' => 'blog',
    'vkontakte' => 'vk',
    'mymailru' => 'myp',
    'twitter' => 'twit',
    'google' => 'google',
    'liveid' => 'lid',
    'odnoklassniki' => 'odnoclass'
);
?>
<a href="javascript:void(0)" class="authbtn login btn authbtn-socserv-login authbtn-bth-hide"><?=GetMessage("AUTH_LOGIN_BUTTON")?></a>
<!-- Соц. сети -->
<div class="choice-soc bx-auth-serv-icons">
    <select class="selectpicker" name="soc-auth-list" style="display: none;">
        <option data-icon="icon-user" value="user"><?=GetMessage('MY_ACCOUNT')?></option>
        <?foreach($arParams["~AUTH_SERVICES"] as $service): $iconClass = (isset($cssClasses[htmlspecialcharsbx($service["ICON"])])) ? $cssClasses[$service["ICON"]] : 'openid';?>
            <option  data-icon="icon-<?=$iconClass?>" value="<?=$service["ID"]?>"><?=htmlspecialcharsbx($service["NAME"])?></option>
        <?endforeach?>
    </select>
</div>
<script type="text/javascript">
    choiseAuthSocNetwork('<?=$arParams["SUFFIX"]?>');
</script>
<??>