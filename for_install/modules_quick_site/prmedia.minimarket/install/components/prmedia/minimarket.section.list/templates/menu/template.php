<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<div></div>
<?php if (!empty($arResult['SECTION_LIST'])): ?>
	<ul>
		<?php foreach ($arResult['SECTION_LIST'] as $arSection): ?>
			<li>
				<div class="smenu">
					<div class="dash"></div>
					<a href="<?php echo $arSection['SECTION_PAGE_URL'] ?>"><?php echo $arSection['NAME'] ?></a>
					<?php if (!empty($arSection['SECTION_LIST'])): ?>
						<div class="trg"></div>
					<?php endif; ?>
				</div>
				<?php if (!empty($arSection['SECTION_LIST'])): ?>
					<ul>
						<?php foreach ($arSection['SECTION_LIST'] as $arSubSection): ?>
							<li>
								<div class="smenu">
									<div class="dash"></div>
									<a href="<?php echo $arSubSection['SECTION_PAGE_URL'] ?>"><?php echo $arSubSection['NAME'] ?></a>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>