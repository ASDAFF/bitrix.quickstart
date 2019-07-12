

<?if (strpos($APPLICATION->GetCurDir(), "/catalog/")===false) {?>
</div>
<?}?>
							</td>
					</tr>

					</table>	
				</td>
				<td class="pr_rc" width="1%">	
				</td>
			</tr>
			<tr>
				<td class="pr_ln" width="1%" >
				</td>
				<td class="pr_n" width="80%">
				</td>
				<td class="pr_rn" width="1%" >
				</td>
			</tr>
		</table>


		<?
							$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_bottom", array(
							"ROOT_MENU_TYPE" => "bottom_main",
							"MENU_CACHE_TYPE" => "N",
							"MENU_CACHE_TIME" => "36000000",
							"MENU_CACHE_USE_GROUPS" => "Y",
							"MENU_CACHE_GET_VARS" => array(
							),
							"MAX_LEVEL" => "2",
							"CHILD_MENU_TYPE" => "bottom2",
							"USE_EXT" => "Y",
							"DELAY" => "N",
							"ALLOW_MULTI_SELECT" => "N"
							),
							false
						);?>
		
		
		<table class="bot" width="100%" cellpadding="" cellspacing="0" border="0">
			<tr>
				<td class="bot_l" width="2%">
				</td>
				<td width="96%">
					<table width="100%" border="0">
						<tr>
							<td width="33%">
								<div id="l">
									<div class="copy"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/copyright.php"), false);?></div>
								</div>
							</td>
							<td width="33%">
								<div id="c">
									<?
										$APPLICATION->IncludeComponent("bitrix:menu", "bottom2", Array(
	"ROOT_MENU_TYPE" => "bottom",	// Тип меню для первого уровня
	"MENU_CACHE_TYPE" => "A",	// Тип кеширования
	"MENU_CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
	"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
	"MAX_LEVEL" => "4",	// Уровень вложенности меню
	"CHILD_MENU_TYPE" => "left2",	// Тип меню для остальных уровней
	"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	"DELAY" => "N",	// Откладывать выполнение шаблона меню
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
);
									?>
								</div>
							</td>
							<td width="33%">
								<div id="r">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/made_in.php"), false);?>
								</div>
							</td>
						</tr>
					</table>
				</td>
				<td class="bot_r" width="2%">
				</td>
			</tr>
		</table>
		
	</div>

</div>
</div>

</body>
</html>
<?
	if ($APPLICATION->GetProperty("CATALOG_COMPARE_LIST", false) == false && IsModuleInstalled('iblock'))
	{
		$arFilter = Array("TYPE"=>"catalog", "SITE_ID"=>SITE_ID);
		$obCache = new CPHPCache;
		if($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog/active"))
		{
			$arIBlocks = $obCache->GetVars();
		}
		elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache())
		{

			$arIBlocks = array();
			$dbRes = CIBlock::GetList(Array(), $arFilter);
			$dbRes = new CIBlockResult($dbRes);

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/iblock/catalog/active");
				
				while($arIBlock = $dbRes->GetNext())
				{
					$CACHE_MANAGER->RegisterTag("iblock_id_".$arIBlock["ID"]);

					if($arIBlock["ACTIVE"] == "Y")
						$arIBlocks[$arIBlock["ID"]] = $arIBlock;
				}

				$CACHE_MANAGER->RegisterTag("iblock_id_new");
				$CACHE_MANAGER->EndTagCache();
			}
			else
			{
				while($arIBlock = $dbRes->GetNext())
				{
					if($arIBlock["ACTIVE"] == "Y")
						$arIBlocks[$arIBlock["ID"]] = $arIBlock;
				}
			}

			$obCache->EndDataCache($arIBlocks);
		}
		else
		{
			$arIBlocks = array();
		}

		if(count($arIBlocks) == 1)
		{
			foreach($arIBlocks as $iblock_id => $arIBlock)
				$APPLICATION->IncludeComponent(
					"bitrix:catalog.compare.list",
					"store",
					Array(
						"IBLOCK_ID" => $iblock_id,
						"COMPARE_URL" => $arIBlock["LIST_PAGE_URL"]."compare/"
					),
					false,
					Array("HIDE_ICONS" => "Y")
				);
		}
	}

	?>
