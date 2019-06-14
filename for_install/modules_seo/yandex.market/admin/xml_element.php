<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

$isPopup = (isset($_REQUEST['popup']) && $_REQUEST['popup'] === 'Y');

if ($isPopup)
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
}
else
{
	require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
}

Loc::loadMessages(__FILE__);

if (!$USER->IsAdmin())
{
	$APPLICATION->AuthForm(Loc::getMessage('YANDEX_MARKET_ADMIN_XML_ELEMENT_REQUIRE_MODULE'));
	return;
}
else if (!Main\Loader::includeModule('yandex.market'))
{
	\CAdminMessage::ShowMessage([
        'TYPE' => 'ERROR',
        'MESSAGE' => Loc::getMessage('YANDEX_MARKET_ADMIN_XML_ELEMENT_REQUIRE_MODULE')
    ]);
}
else
{
	Market\Metrika::load();

	$APPLICATION->SetAdditionalCSS('/bitrix/css/yandex.market/base.css');

	try
	{
		$request = Main\Context::getCurrent()->getRequest();

		$stepName = trim($request->get('type'));
		$setupId = (int)$request->get('setup');
		$elementId = trim($request->get('id'));

		$setup = Market\Export\Setup\Model::loadById($setupId);
		$processor = new Market\Export\Run\Processor($setup);
		$step = Yandex\Market\Export\Run\Manager::getStepProvider($stepName, $processor);
		$tag = $step->getTag();
		$tagName = $tag->getName();
		$writer = $processor->getWriter();

		$contents = $writer->searchTag($tagName, $elementId);

		if ($contents !== null)
		{
			$contents = Market\Utils::prettyPrintXml($contents);

			echo '<pre class="b-code">';
			echo htmlspecialcharsbx($contents);
			echo '</pre>';
		}
		else
		{
			throw new Main\ObjectNotFoundException(Market\Config::getLang('ADMIN_XML_ELEMENT_NOT_FOUND'));
		}
	}
	catch (Main\SystemException $exception)
	{
		\CAdminMessage::ShowMessage([
            'TYPE' => 'ERROR',
            'MESSAGE' => $exception->getMessage()
        ]);
	}
}

if ($isPopup)
{
	require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_popup_admin.php';
}
else
{
	require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
}