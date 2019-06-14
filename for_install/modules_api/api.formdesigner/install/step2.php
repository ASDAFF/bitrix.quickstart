<?
/** @var CMain $APPLICATION */

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $arErrors;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$APPLICATION->SetTitle(Loc::getMessage('AFD_STEP2_PAGE_TITLE'));

if(!check_bitrix_sessid())
	return Loc::getMessage('AFD_STEP2_SESS_EXPIRED');

if($arErrors)
	echo CAdminMessage::ShowMessage(join('<br>', $arErrors));
else
	echo CAdminMessage::ShowNote(GetMessage('AFD_STEP2_INSTALL_OK'));
?>
<?
if($request->get('INSTALL_DEMO') == 'Y')
{
	echo BeginNote();
	echo Loc::getMessage('AFD_STEP2_INSTALL_NOTE');
	echo EndNote();
}
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=Loc::getMessage('AFD_INSTALL_BUTTON_BACK')?>">
</form>