<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<nav>
	<ul>
		<?php foreach ($arResult as $menuItem): ?>
			<li><a href="<?php echo $menuItem['LINK'] ?>"><?php echo $menuItem['TEXT'] ?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>