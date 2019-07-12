<? IncludeTemplateLangFile(__FILE__); ?>


						<? if(strpos($APPLICATION->GetCurDir(), "about")): ?></div><? endif; ?>
						<div style="clear:both"></div>
					</div>


					<!-- center -->
				</div>

			</div>

			<div style="clear:both"></div>
		</div>

		<div id="footer">
			<div id="foot-wrap">

				<div id="fcrop"></div>
				<div id="copy"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/copyright.php"), false);?></div>
				<div id="contacts">
					<strong><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/phone_simple.php"), false);?></strong>
					<a href="<?=SITE_DIR?>about/contacts/"><i></i><?=GetMessage("CONTACTS")?></a>
				</div>
				<ul id="bot-links">
		            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."includes/social.php"), false);?>
				</ul>
				<div id="dev">
					<span><a href="http://www.b-tricks.ru/"><?=GetMessage("COPY")?></a></span>
					<a href="http://www.b-tricks.ru/"><img src="<?=SITE_TEMPLATE_PATH?>/images/btricks.png" alt="B-Tricks" /></a>
				</div>

			</div>
		</div>




	</body>
</html>