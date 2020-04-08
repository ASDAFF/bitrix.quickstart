<table class="product-list">
	<tbody>
		<tr>
			<?
			foreach($result['PRODUCT_LIST_CONFIG']['PAGE_ELEMENT_COL_OPTION'] as $arCol) {
				?>
				<th>
					<?
					if($arCol['SORT']) {
						?>
						<div class="js-change-element-sort sort<?=$arCol['SORT'] == $result['PRODUCT_LIST_CONFIG']['PAGE_ELEMENT_SORT']['FIELD'] ? ($result['PRODUCT_LIST_CONFIG']['PAGE_ELEMENT_SORT']['ORDER'] == 'ASC' ? ' up' : ' down') : ''?>" data-sort="<?=$arCol['SORT']?>" data-name="<?=$result['PRODUCT_LIST_CONFIG']['PAGE_ELEMENT_NAME']?>">
							<?=$arCol['NAME']?>
							<span class="up">&uarr;</span>
							<span class="down">&darr;</span>
						</div>
						<?
					}
					else {
						?><?=$arCol['NAME']?><?
					}
					?>
				</th>
				<?
			}
			?>
		</tr>
		<?
		foreach($result["ROWS"] as $arRow) {
			$result['this']->AddEditAction($arRow['ITEM']['ID'], $arRow['ITEM']['EDIT_LINK'], CIBlock::GetArrayByID($arRow['ITEM']["IBLOCK_ID"], "ELEMENT_EDIT"));
			$result['this']->AddDeleteAction($arRow['ITEM']['ID'], $arRow['ITEM']['DELETE_LINK'], CIBlock::GetArrayByID($arRow['ITEM']["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<tr id="<?=$result['this']->GetEditAreaId($arRow['ITEM']['ID']);?>">
				<?
				foreach($arRow['ROW'] as $col) {
					?>
					<td><?=$col?></td>
					<?
				}
				?>
			</tr>
			<?
		}
		?>
	</tbody>
</table>
