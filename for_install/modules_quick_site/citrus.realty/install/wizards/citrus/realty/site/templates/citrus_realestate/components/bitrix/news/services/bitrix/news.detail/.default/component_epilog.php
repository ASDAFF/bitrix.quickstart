<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams Параметры, чтение/изменение не затрагивает одноименный член компонента. */
/** @var array $arResult Результат, чтение/изменение не затрагивает одноименный член класса компонента. */
/** @var string $componentPath Путь к папке с компонентом от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list). */
/** @var CBitrixComponent $component Ссылка на $this. */
/** @var CBitrixComponent $this Ссылка на текущий вызванный компонент, можно использовать все методы класса. */
/** @var string $epilogFile Путь к файлу component_epilog.php относительно DOCUMENT_ROOT */
/** @var string $templateName Имя шаблона компонента (например: .dеfault) */
/** @var string $templateFile Путь к файлу шаблона от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateFolder Путь к папке с шаблоном от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData Обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные закешируются и будут доступны в component_epilog.php на каждом хите */

CJsCore::Init('fancybox');
?>
<script type="text/javascript">
	$(document).ready(function() {
		$(".popup[rel=news-detail-photo]").fancybox();
	});
</script>
