<?
if ($contact = $arResult['CONTACT'])
{
	?>
	<div class="contact-corner">
		<h3><?=GetMessage("CITRUS_REALTY_SIDEBAR_TITLE")?></h3>
		<div class="contact-corner-recv">
			<p style="max-height: 1.6em; overflow: hidden;"><?=GetMessage("CITRUS_REALTY_SIDEBAR_EXPERT")?><a href="<?=$contact["DETAIL_PAGE_URL"]?>"><?=$contact["NAME"]?></a></p>
		</div>
		<?
		if (isset($contact['office']) && is_array($contact['office']))
		{
			?><div class="contact-corner-recv">
				<?=(GetMessage("CITRUS_REALTY_SIDEBAR_OFFICE") . $contact["office"]["NAME"])?>
				<div class="on-map"><a href="javascript:void(0)" data-address="<?=$contact["office"]["PROPERTY_ADDRESS_VALUE"]?>" class="map-link"><?=GetMessage("CITRUS_REALTY_ON_MAP")?></a></div>
			</div>
			<?
		}

		$phones = $mails = array();
		// телефоны из контактной информации по сотруднику
		if (is_array($contact["PROPERTY_CONTACTS_VALUE"]))
		{
			foreach ($contact["PROPERTY_CONTACTS_VALUE"] as $contactInfo)
			{
                if (preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{6,10}$/m', $contactInfo))
					$phones[] = $contactInfo;
				/*elseif (check_email($contactInfo))
					$mails[] = $contactInfo;*/
			}
		}
		// телефон офиса
		if (is_array($contact['office']) && is_array($contact['office']['PROPERTY_PHONES_VALUE']))
			$phones = array_merge($phones, $contact['office']['PROPERTY_PHONES_VALUE']);

		$phones = array_unique($phones);
		if (!empty($phones))
		{
			?><div class="contact-corner-phone"><?
			foreach ($phones as $phone)
			{
				?><p><?=$phone?></p><?
			}
			?></div><?
		}

		if (is_array($contact['office']) && is_array($contact['office']['PROPERTY_SCHEDULE_VALUE']))
		{
			?><div class="contact-corner-sked"><?
			foreach ($contact['office']['PROPERTY_SCHEDULE_VALUE'] as $key=>$value)
			{
				$desc = $contact['office']['PROPERTY_SCHEDULE_DESCRIPTION'][$key];
				$desc = strlen($desc) ? $desc . ': ' : '';
				?><p><?=($desc . $value)?></p><?
			}
			?></div><?
		}

		?>
		<div class="contact-corner-button print-hidden">
			<a href="<?=SITE_DIR?>ajax/request.php?ID=<?=$arResult["ID"]?>" class="ajax-popup"><?=GetMessage("CITRUS_REALTY_SIDEBAR_REQUEST")?></a>
		</div>
	</div>
	<?
}
?>
<div class="case case-marg print-hidden">
	<ul>
		<li class="case-add"><a href="javascript:void()" class="add2favourites" data-id="<?=$arResult["ID"]?>"><?=GetMessage("CITRUS_REALTY_ADD_TO_FAV")?></a></li>
		<li class="case-email"><a href="<?=SITE_DIR?>ajax/share_via_email.php" class="ajax-popup"><?=GetMessage("CITRUS_REALTY_SHARE_EMAIL")?></a></li>
		<li class="case-print"><a href="javascript:window.print()"><?=GetMessage("CITRUS_REALTY_PRINT_VERSION")?></a></li>
	</ul>
</div>
