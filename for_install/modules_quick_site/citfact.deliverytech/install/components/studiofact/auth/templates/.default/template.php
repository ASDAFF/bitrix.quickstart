<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->createFrame()->begin("");  ?>
<ul><? global $USER;
if ($USER->IsAuthorized()) {
	?><li class="inline"><a href="<?=SITE_DIR;?>personal/" class="auth" title="<?=GetMessage("STUDIOFACT_PERSONAL");?>"><?=GetMessage("STUDIOFACT_PERSONAL");?></a></li>
	<li class="inline"><a href="<?=SITE_DIR;?>?logout=yes" title="<?=GetMessage("STUDIOFACT_EXIT");?>"><?=GetMessage("STUDIOFACT_EXIT");?></a></li><?
} else {
	?><li class="inline"><a href="<?=SITE_DIR;?>auth/" class="open_auth auth" title="<?=GetMessage("STUDIOFACT_AUTH");?>"><?=GetMessage("STUDIOFACT_AUTH");?></a></li>
	<li class="inline"><a href="<?=SITE_DIR;?>auth/?register=yes" title="<?=GetMessage("STUDIOFACT_REGISTER");?>"><?=GetMessage("STUDIOFACT_REGISTER");?></a></li><?
} ?></ul>