<?
function SonetShowInFrame(&$component)
{
	global $APPLICATION;

	$APPLICATION->RestartBuffer();

	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
		<head>
			<?$APPLICATION->ShowHead();?>
			<style>
				body {background: #fff !important; text-align: left !important; color: #000 !important;}
				div#sonet-content-outer { padding: 15px; }
			</style>
		</head>
		<body class="<?$APPLICATION->ShowProperty("BodyClass");?>">
			<div id="sonet-content-outer">
				<table cellpadding="0" cellspading="0" width="100%">
					<tr>
						<td valign="top"><?php $component->IncludeComponentTemplate();?></td>
					</tr>
				</table>
			</div>
		</body>
	</html><?
	die();
}
?>