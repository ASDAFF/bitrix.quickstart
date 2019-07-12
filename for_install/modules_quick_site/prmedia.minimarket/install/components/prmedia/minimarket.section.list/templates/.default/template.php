<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php if (!empty($arResult['SECTION_LIST'])): ?>
	<nav>
		<ul>
			<?php foreach ($arResult['SECTION_LIST'] as $arSection): ?>
				<li>
					<a <?php echo empty($arSection['SELECTED']) ? '' : 'class="active"' ?> href="<?php echo $arSection['SECTION_PAGE_URL'] ?>"><?php echo $arSection['NAME'] ?></a>
					<?php if (!empty($arSection['SECTION_LIST'])): ?>
						<ul>
							<?php foreach ($arSection['SECTION_LIST'] as $arSubSection): ?>
								<li>
									<a <?php echo empty($arSubSection['SELECTED']) ? '' : 'class="active"' ?> href="<?php echo $arSubSection['SECTION_PAGE_URL'] ?>">
										<?php echo $arSubSection['NAME'] ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
<?php endif; ?>