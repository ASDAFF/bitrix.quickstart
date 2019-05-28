<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;

/** @var $tag \Yandex\Market\Export\Xml\Tag\Base */
/** @var $tagId string */
/** @var $tagName string */

$supportWarnings = [
	'param' => true
];

if (isset($supportWarnings[$tagId]))
{
	?>
	<tr class="b-param-table__warning is--empty js-param-tag__warning-wrap">
		<td class="b-param-table__cell" align="right" width="40%"></td>
		<td class="b-param-table__cell js-param-tag__warning-place" colspan="3"></td>
	</tr>
	<?
}