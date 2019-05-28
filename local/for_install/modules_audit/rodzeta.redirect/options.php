<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\{Application, Localization\Loc};

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new \CAdminTabControl("tabControl", [
  [
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_REDIRECT_BTN_OPTIONS"),
		"TITLE" => Loc::getMessage("RODZETA_REDIRECT_OPTIONS_TITLE"),
  ],
  [
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_REDIRECT_BTN_REDIRECTS"),
		"TITLE" => Loc::getMessage("RODZETA_REDIRECT_TITLE_REDIRECTS"),
  ],  
]);

if (check_bitrix_sessid() && $request->isPost()) {
	if ($request->getPost("save") != "") {
		$data = $request->getPostList();
		OptionsUpdate($data);
		Update($data);
	}
}
$currentOptions = Options();

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_WWW_TITLE") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_www" value="Y" type="checkbox"
				<?= $currentOptions["redirect_www"] == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_HTTPS_TITLE") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<label>
				<input name="redirect_https" value="" type="radio"
					<?= $currentOptions["redirect_https"] == ""? "checked" : "" ?>>
				<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_HTTPS_NO") ?>
			</label>
			<br>
			<label>
				<input name="redirect_https" value="to_https" type="radio"
					<?= $currentOptions["redirect_https"] == "to_https"? "checked" : "" ?>>
				<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_HTTPS_HTTPS") ?>
			</label>
			<br>
			<label>
				<input name="redirect_https" value="to_http" type="radio"
					<?= $currentOptions["redirect_https"] == "to_http"? "checked" : "" ?>>
				<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_HTTPS_HTTP") ?>
			</label>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_SLASH_TITLE") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_slash" value="Y" type="checkbox"
				<?= $currentOptions["redirect_slash"] == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_INDEX_TITLE") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_index" value="Y" type="checkbox"
				<?= $currentOptions["redirect_index"] == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("RODZETA_REDIRECT_OPTIONS_MULTISLASH_TITLE") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_multislash" value="Y" type="checkbox"
				<?= $currentOptions["redirect_multislash"] == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("RODZETA_REDIRECT_IGNORE_QUERY") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="ignore_query" value="Y" type="checkbox"
				<?= $currentOptions["ignore_query"] == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("RODZETA_REDIRECT_FROM_404") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="redirect_from_404" value="Y" type="checkbox"
				<?= $currentOptions["redirect_from_404"] == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("RODZETA_REDIRECT_URLS_TITLE") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="use_redirect_urls" value="Y" type="checkbox"
				<?= $currentOptions["use_redirect_urls"] == "Y"? "checked" : "" ?>>
			<?= str_replace($_SERVER["DOCUMENT_ROOT"], "", FILE_REDIRECTS) ?>
		</td>
	</tr>
	
	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td colspan="2">

			<table width="100%" class="js-table-autoappendrows">
				<tbody>
					<?php
					$i = 0;
					foreach (AppendValues(Select(true), 5, ["", "", ""]) as $url) {
						$i++;
					?>
						<tr data-idx="<?= $i ?>">
							<td>
								<input type="text" placeholder="<?=
										Loc::getMessage("RODZETA_REDIRECT_URLS_FROM") ?>"
									name="redirect_urls[<?= $i ?>][0]"
									value="<?= htmlspecialcharsex($url[0]) ?>"
									style="width:96%;">
							</td>
							<td>
								<input type="text" placeholder="<?=
										Loc::getMessage("RODZETA_REDIRECT_URLS_TO") ?>"
									name="redirect_urls[<?= $i ?>][1]"
									value="<?= htmlspecialcharsex($url[1]) ?>"
									style="width:96%;">
							</td>
							<td>
								<select name="redirect_urls[<?= $i ?>][2]"
										title="<?= Loc::getMessage("RODZETA_REDIRECT_URLS_STATUS") ?>"
										style="width:96%;">
									<option value="301" <?= $url[2] == "301"? "selected" : "" ?>>301</option>
									<option value="302" <?= $url[2] == "302"? "selected" : "" ?>>302</option>
								</select>
							</td>
							<td>
								<input name="redirect_urls[<?= $i ?>][3]" value="Y" type="checkbox"
									title="<?= Loc::getMessage("RODZETA_REDIRECT_URLS_IS_PART_URL") ?>"
									<?= $url[3] == "Y"? "checked" : "" ?>>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save"
  	value="<?= Loc::getMessage("RODZETA_REDIRECT_SAVE_SETTINGS") ?>">

</form>

<?php

$tabControl->end();

?>

<script>

BX.ready(function () {
	"use strict";
	// autoappend rows
	function makeAutoAppend($table) {
		function bindEvents($row) {
			for (let $input of $row.querySelectorAll('input[type="text"]')) {
				$input.addEventListener("change", function (event) {
					let $tr = event.target.closest("tr");
					let $trLast = $table.rows[$table.rows.length - 1];
					if ($tr != $trLast) {
						return;
					}
					$table.insertRow(-1);
					$trLast = $table.rows[$table.rows.length - 1];
					$trLast.innerHTML = $tr.innerHTML;
					let idx = parseInt($tr.getAttribute("data-idx")) + 1;
					$trLast.setAttribute("data-idx", idx);
					for (let $input of $trLast.querySelectorAll("input,select")) {
						let name = $input.getAttribute("name");
						if (name) {
							$input.setAttribute("name", name.replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
						}
					}
					bindEvents($trLast);
				});
			}
		}
		for (let $row of document.querySelectorAll(".js-table-autoappendrows tr")) {
			bindEvents($row);
		}
	}
	for (let $table of document.querySelectorAll(".js-table-autoappendrows")) {
		makeAutoAppend($table);
	}
});

</script>
