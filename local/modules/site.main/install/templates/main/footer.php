<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (defined('\Site\Main\IS_AJAX') && \Site\Main\IS_AJAX) {
	return;
}
?>
		</div>
		
		<footer id="footer" class="container">
			<div class="navbar-toolbar">
				<?$APPLICATION->IncludeComponent(
					'bitrix:menu',
					'.default',
					array(
						'ROOT_MENU_TYPE' => 'top',
						'MAX_LEVEL' => '1',
						'CHILD_MENU_TYPE' => 'left',
						'USE_EXT' => 'N',
						'DELAY' => 'N',
						'ALLOW_MULTI_SELECT' => 'N',
						'MENU_CACHE_TYPE' => 'A',
						'MENU_CACHE_TIME' => '3600',
						'MENU_CACHE_USE_GROUPS' => 'N',
						'MENU_CACHE_GET_VARS' => array(
						)
					),
					false
				)?>
			</div>
			
			<div class="row">
				<div class="col-sm-4">
					<div class="navbar-toolbar">
						<?$APPLICATION->IncludeComponent(
							'site:subscribtion',
							'.default',
							array(
								'SET_TITLE' => 'N'
							),
							false
						)?>
					</div>
				</div>
				<div class="col-sm-4">
				</div>
				<div class="col-sm-4">
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-4">
					<div class="navbar-toolbar">
						<?$APPLICATION->IncludeFile(
							'/includes/footer-copy.php',
							array(),
							array(
								'MODE' => 'php',
							)
						)?>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="navbar-toolbar">
						<?$APPLICATION->IncludeFile(
							'/includes/footer-sitemap.php',
							array(),
							array(
								'MODE' => 'php',
							)
						)?>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="navbar-toolbar">
						<?$APPLICATION->IncludeFile(
							'/includes/footer-dev.php',
							array(),
							array(
								'MODE' => 'php',
							)
						)?>
					</div>
				</div>
			</div>
		</footer>
		
		<div id="go-top" class="visible-lg-block">
			<a class="fake" href="#top"><?=GetMessage('TEMPLATE_GO_TOP')?></a>
		</div>
		
		<div id="loading-indicator-template" class="hidden">
			<?$APPLICATION->IncludeFile(
				'includes/loading-indicator.php',
				array(),
				array(
					'SHOW_BORDER' => false,
				)
			)?>
		</div>
		
		<?$APPLICATION->IncludeComponent(
			'site:no.old.browser',
			'',
			array(),
			false,
			array(
				'HIDE_ICONS' => 'Y',
			)
		)?>
		
		<?/*<script src="<?=SITE_TEMPLATE_PATH?>/js/crosspixel/crosspixel.js"></script>*/?>
	</body>
</html>