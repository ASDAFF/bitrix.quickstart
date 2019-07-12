<?IncludeTemplateLangFile(__FILE__);?>
				</div><!-- #content-->
				<?
				if (!$bCitrusTemplateIndex)
				{
					?>
					<aside id="sideRight" class="sidebar<?$APPLICATION->ShowViewContent('sidebarClass')?>">
						<?$APPLICATION->ShowViewContent('sidebar')?>
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"",
							Array(
								"AREA_FILE_SHOW" => "sect", // page | sect - area to include
								"AREA_FILE_SUFFIX" => "side", // suffix of file to seek
								"EDIT_MODE" => "php",
							),
							false
						);?>
					</aside><!-- #sideRight -->
					<?
				}
				else
				{
					?>
					<div class="corner">
						<?$APPLICATION->ShowViewContent('sidebar')?>
						<?$APPLICATION->IncludeComponent(
						"bitrix:main.include", "", array(
							"AREA_FILE_SHOW" => "sect",
							"AREA_FILE_SUFFIX" => "corner",
							"AREA_FILE_RECURSIVE" => "Y",
							"EDIT_TEMPLATE" => ""
						),
						false
						);?>
					</div>
					<?
				}
				?>
			</div><!-- #container-->
		</div>
	</div>
</div><!-- #wrapper -->
<?
if ($bCitrusTemplateIndex)
{
	?><div id="house"></div><?
}

?>
<div class="footer-block">
<?
if ($bCitrusTemplateIndex)
{
	?>
	<div class="column">

		<div class="block">
			<?$APPLICATION->IncludeComponent(
			"bitrix:main.include", "", array(
				"AREA_FILE_SHOW" => "sect",
				"AREA_FILE_SUFFIX" => "advert",
				"AREA_FILE_RECURSIVE" => "Y",
				"EDIT_TEMPLATE" => ""
			),
			false
			);?>
		</div>
	</div><!-- #column -->
	<?
}
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"bottom",
	Array(
		"AREA_FILE_SHOW" => "page", // page | sect - area to include
		"AREA_FILE_SUFFIX" => "bottom", // suffix of file to seek
		"EDIT_MODE" => "php",
	),
false
);?>
<?

$APPLICATION->ShowViewContent('footer-block');

if (!$bCitrusTemplateIndex)
{
	?>
		<div class="info info-house">
			<div class="block">
				<div class="footer-bar-inner">
					<?$APPLICATION->IncludeComponent(
						"bitrix:main.include",
						"",
						Array(
							"AREA_FILE_SHOW" => "file", // page | sect - area to include
							"AREA_FILE_SUFFIX" => "inc", // suffix of file to seek
							"AREA_FILE_RECURSIVE" => "Y",
							"PATH" => SITE_DIR . "include/footer_bar.php",
							"EDIT_TEMPLATE" => "page_inc.php",
							"EDIT_MODE" => "php",
						),
						false
					);?>
				</div>
			</div>
		</div><!-- INFO -->
	<div id="footer-house"></div>
	<?
}
?>

<footer id="footer">
	<div class="block">
	<?
		if (!$bCitrusTemplateIndex)
		{
			?>
			<div class="contacts">
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
						"AREA_FILE_SHOW" => "file", // page | sect - area to include
						"AREA_FILE_SUFFIX" => "inc", // suffix of file to seek
						"AREA_FILE_RECURSIVE" => "Y",
						"PATH" => SITE_DIR . "include/footer_contacts.php",
						"EDIT_TEMPLATE" => "page_inc.php",
						"EDIT_MODE" => "php",
					),
					false
				);?>
			</div>
			<?
		}
	?>
		<div class="footer-border fregat"></div>
		<div class="footer-border counter"></div>
		<div class="footer-copyright">
			<?$APPLICATION->IncludeComponent(
				"bitrix:main.include",
				"",
				Array(
					"AREA_FILE_SHOW" => "file", // page | sect - area to include
					"AREA_FILE_SUFFIX" => "inc", // suffix of file to seek
					"AREA_FILE_RECURSIVE" => "Y",
					"PATH" => SITE_DIR . "include/footer_text.php",
					"EDIT_TEMPLATE" => "page_inc.php",
					"EDIT_MODE" => "php",
				),
			false
			);?>
			<p><?=GetMessage("COPYRIGHT");?> <a href="http://citrus-soft.ru" target="_blank"><?=GetMessage("COPYRIGHT_CITRUS");?></a></p>
		</div>
		<div class="footer-counter">
			<?$APPLICATION->IncludeComponent(
				"bitrix:main.include",
				"",
				Array(
					"AREA_FILE_SHOW" => "file", // page | sect - area to include
					"AREA_FILE_SUFFIX" => "inc", // suffix of file to seek
					"AREA_FILE_RECURSIVE" => "Y",
					"PATH" => SITE_DIR . "include/footer_counters.php",
					"EDIT_TEMPLATE" => "page_inc.php",
					"EDIT_MODE" => "php",
				),
				false
			);?>
		</div>
	</div>
</footer><!-- #footer -->
</div>
</body>
</html>