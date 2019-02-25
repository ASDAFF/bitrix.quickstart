<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo '<ul class="sitemap">';

$prevLevel = 0;
foreach ($arResult['MAP_CONTENT'] as $mapElement)
{
	if (empty($mapElement['NAME']))
	{
		$mapElement['NAME'] = '&lt;No name&gt;';
	}

	if ($prevLevel > $mapElement['LEVEL'])
	{
		echo str_repeat('</ul></li>', $prevLevel - $mapElement['LEVEL']).'<li>';
	}
	else if ($prevLevel < $mapElement['LEVEL'])
	{
		echo '<ul class="level-'.$mapElement['LEVEL'].'"><li>';
	}
	else
	{
		echo ($prevLevel != 0 ? '</li>' : '').'<li>';
	}

	echo '<a href="'.$mapElement['LINK'].'">'.$mapElement['NAME'].'</a>';

	$prevLevel = $mapElement['LEVEL'];
}

echo '</ul>';