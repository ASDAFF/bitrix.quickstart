
</article>
			</section>
		</div><!--/content-->
		<footer class="b-footer clearfix">
			<div class="b-developer">
				<div class="b-footer__title">Разработано в <a href="http://aeroidea.ru/">Aero</a></div>
				<a href="http://aeroidea.ru/" class="b-devepoper__logo"></a>
			</div>
<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom_nav_menu", array(
	"ROOT_MENU_TYPE" => "bottom",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "/includes/subscribe.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
			<div class="b-footer-socail">
<div class="b-footer__title">Поделиться</div>
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "/includes/social.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
			</div>
<div class="b-footer-counters">
<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "/includes/counters.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
</div>
		</footer><!--/footer-->
	</div><!--/.b-wrapper-->
</div>
</body>
</html>