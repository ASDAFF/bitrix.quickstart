<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="search-page search-page-default">
	<div class="panel panel-default">
		<div class="panel-body">
			<form class="form-inline form-search" action="" method="get" role="search">
				<div class="form-group">
					<?
					if ($arParams['USE_SUGGEST'] == 'Y') {
						if (strlen($arResult['REQUEST']['~QUERY']) && is_object($arResult['NAV_RESULT'])) {
							$arResult['FILTER_MD5'] = $arResult['NAV_RESULT']->GetFilterMD5();
							$obSearchSuggest = new CSearchSuggest($arResult['FILTER_MD5'], $arResult['REQUEST']['~QUERY']);
							$obSearchSuggest->SetResultCount($arResult['NAV_RESULT']->NavRecordCount);
						}
						
						$APPLICATION->IncludeComponent(
							'bitrix:search.suggest.input',
							'',
							array(
								'NAME' => 'q',
								'VALUE' => $arResult['REQUEST']['~QUERY'],
								'INPUT_SIZE' => 40,
								'DROPDOWN_SIZE' => 10,
								'FILTER_MD5' => $arResult['FILTER_MD5'],
							),
							$component,
							array(
								'HIDE_ICONS' => 'Y',
							)
						);
					} else {
						?>
						<input class="form-control" type="text" name="q" value="<?=$arResult['REQUEST']['QUERY']?>" placeholder="<?=GetMessage('SEARCH_LABEL')?>" required=""/>
						<?
					}
					?>
				</div>
				<?
				
				if ($arParams['SHOW_WHERE']) {
					?>
					<div class="form-group">
						<select class="form-control" name="where">
							<option value=""><?=GetMessage('SEARCH_ALL')?></option>
							<?foreach($arResult['DROPDOWN'] as $key => $value):?>
								<option value='<?=$key?>'<?=$arResult['REQUEST']['WHERE'] == $key ? ' selected=""' : ''?>>
									<?=$value?>
								</option>
							<?endforeach?>
						</select>
					</div>
					<?
				}
				
				?>
				<div class="form-group">
					<input class="btn btn-default" type="submit" value="<?=GetMessage('SEARCH_GO')?>"/>
					<input type="hidden" name="how" value="<?=$arResult['REQUEST']['HOW'] == 'd' ? 'd' : 'r'?>"/>
				</div>
				<?
				
				if ($arParams['SHOW_WHEN']) {
					?>
					<div class="form-group">
						<label><?=GetMessage('CT_BSP_ADDITIONAL_PARAMS')?>:</label>
						<?$APPLICATION->IncludeComponent(
							'bitrix:main.calendar',
							'',
							array(
								'SHOW_INPUT' => 'Y',
								'INPUT_NAME' => 'from',
								'INPUT_VALUE' => $arResult['REQUEST']['~FROM'],
								'INPUT_NAME_FINISH' => 'to',
								'INPUT_VALUE_FINISH' => $arResult['REQUEST']['~TO'],
								'INPUT_ADDITIONAL_ATTR' => 'class="form-control"',
							),
							null,
							array('HIDE_ICONS' => 'Y')
						)?>
					</div>
					<?
				}?>
			</form>
		</div>
	</div>
	
	<?if(isset($arResult['REQUEST']['ORIGINAL_QUERY'])) {
		?>
		<div class="language-guess">
			<?=GetMessage('CT_BSP_KEYBOARD_WARNING', array('#query#' => '<a href="' . $arResult['ORIGINAL_QUERY_URL'] . '">' . $arResult['REQUEST']['ORIGINAL_QUERY'] . '</a>'))?>
		</div>
		<?
	}?>
	
	<?if ($arResult['REQUEST']['QUERY'] === false
		&& $arResult['REQUEST']['TAGS'] === false
	) {
		
	} elseif ($arResult['ERROR_CODE']) {
		?>
		<p><?=GetMessage('SEARCH_ERROR')?></p>
		<?if ($arResult['ERROR_TEXT']) {
			ShowError($arResult['ERROR_TEXT']);
		}?>
		<p><?=GetMessage('SEARCH_CORRECT_AND_CONTINUE')?></p>
		<p><?=GetMessage('SEARCH_SINTAX')?><br /><b><?=GetMessage('SEARCH_LOGIC')?></b></p>
		<table class="table table-striped">
			<thead>
				<tr>
					<th><?=GetMessage('SEARCH_OPERATOR')?></th>
					<th><?=GetMessage('SEARCH_SYNONIM')?></th>
					<th><?=GetMessage('SEARCH_DESCRIPTION')?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?=GetMessage('SEARCH_AND')?></td>
					<td>and, &amp;, +</td>
					<td><?=GetMessage('SEARCH_AND_ALT')?></td>
				</tr>
				<tr>
					<td><?=GetMessage('SEARCH_OR')?></td>
					<td>or, |</td>
					<td><?=GetMessage('SEARCH_OR_ALT')?></td>
				</tr>
				<tr>
					<td><?=GetMessage('SEARCH_NOT')?></td>
					<td>not, ~</td>
					<td><?=GetMessage('SEARCH_NOT_ALT')?></td>
				</tr>
				<tr>
					<td>( )</td>
					<td>&nbsp;</td>
					<td><?=GetMessage('SEARCH_BRACKETS_ALT')?></td>
				</tr>
			</tbody>
		</table>
		<?
	} elseif (count($arResult['SEARCH'])) {
		?>
		<section>
			<?
			if ($arParams['DISPLAY_TOP_PAGER'] != 'N') {
				print $arResult['NAV_STRING'];
			}
			
			?>
			<div class="search-page-items">
				<?
				foreach($arResult['SEARCH'] as $arItem) {
					?>
					<article class="search-page-item">
						<h2>
							<a href="<?=$arItem['URL']?>"><?=$arItem['TITLE_FORMATED']?></a>
						</h2>
						<p class="body"><?=$arItem['BODY_FORMATED']?></p>
						<?
						if ($arParams['SHOW_RATING'] == 'Y'
							&& strlen($arItem['RATING_TYPE_ID']) > 0
							&& $arItem['RATING_ENTITY_ID'] > 0
						) {
							?>
							<div class="rate"><?
								$APPLICATION->IncludeComponent(
									'bitrix:rating.vote', $arParams['RATING_TYPE'],
									Array(
										'ENTITY_TYPE_ID' => $arItem['RATING_TYPE_ID'],
										'ENTITY_ID' => $arItem['RATING_ENTITY_ID'],
										'OWNER_ID' => $arItem['USER_ID'],
										'USER_VOTE' => $arItem['RATING_USER_VOTE_VALUE'],
										'USER_HAS_VOTED' => $arItem['RATING_USER_VOTE_VALUE'] == 0 ? 'N': 'Y',
										'TOTAL_VOTES' => $arItem['RATING_TOTAL_VOTES'],
										'TOTAL_POSITIVE_VOTES' => $arItem['RATING_TOTAL_POSITIVE_VOTES'],
										'TOTAL_NEGATIVE_VOTES' => $arItem['RATING_TOTAL_NEGATIVE_VOTES'],
										'TOTAL_VALUE' => $arItem['RATING_TOTAL_VALUE'],
										'PATH_TO_USER_PROFILE' => $arParams['~PATH_TO_USER_PROFILE'],
									),
									$component,
									array(
										'HIDE_ICONS' => 'Y'
									)
								);?>
							</div>
							<?
						}
						?>
						<dl class="details text-muted clearfix">
							<dt class="date"><?=GetMessage('SEARCH_MODIFIED')?></dt>
							<dd class="date"><?=$arItem['DATE_CHANGE']?></dd>
							<?if ($arItem['CHAIN_PATH']) {
								?>
								<dt class="path"><?=GetMessage('SEARCH_PATH')?></dt>
								<dd class="path"><?=$arItem['CHAIN_PATH']?></dd>
								<?
							}?>
						</dl>
					</article>
					<?
				}
				?>
			</div>
			<?
			
			if ($arParams['DISPLAY_BOTTOM_PAGER'] != 'N') {
				print $arResult['NAV_STRING'];
			}
			?>
		</section>
		<?
		
		?>
		<p>
			<?
			if ($arResult['REQUEST']['HOW'] == 'd') {
				?><a href="<?=$arResult['URL']?>&amp;how=r<?=$arResult['REQUEST']['FROM'] ? '&amp;from=' . $arResult['REQUEST']['FROM'] : ''?><?=$arResult['REQUEST']['TO'] ? '&amp;to=' . $arResult['REQUEST']['TO']: ''?>"><?=GetMessage('SEARCH_SORT_BY_RANK')?></a>&nbsp;|&nbsp;<b><?=GetMessage('SEARCH_SORTED_BY_DATE')?></b><?
			} else {
				?><b><?=GetMessage('SEARCH_SORTED_BY_RANK')?></b>&nbsp;|&nbsp;<a href="<?=$arResult['URL']?>&amp;how=d<?=$arResult['REQUEST']['FROM'] ? '&amp;from=' . $arResult['REQUEST']['FROM']: ''?><?=$arResult['REQUEST']['TO'] ? '&amp;to=' . $arResult['REQUEST']['TO'] : ''?>"><?=GetMessage('SEARCH_SORT_BY_DATE')?></a><?
			}?>
		</p>
		<?
	} else {
		ShowNote(GetMessage('SEARCH_NOTHING_TO_FOUND'));
	}
	?>
</div>