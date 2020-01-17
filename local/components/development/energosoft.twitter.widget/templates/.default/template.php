<?
######################################################
# Name: energosoft.twitter                           #
# File: template.php                                 #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if($arParams["ES_TWITTER"]!=""):?>
<script>
	new TWTR.Widget({
	  version: 2,
	  type: '<?=$arParams["ES_TYPE"]?>',
	<?if($arParams["ES_TYPE"]=="search"):?>
	  search: '<?=$arParams["ES_SEARCH"]?>',
	<?endif;?>
	<?if($arParams["ES_TYPE"]=="search" || $arParams["ES_TYPE"]=="faves" || $arParams["ES_TYPE"]=="list"):?>
	  title: '<?=$arParams["ES_TITLE"]?>',
	  subject: '<?=$arParams["ES_SUBJECT"]?>',
	<?endif;?>
	  rpp: <?=$arParams["ES_COUNT"]?>,
	<?if($arParams["ES_BEHAVIOR"]=="default"):?>
	  interval: <?=$arParams["ES_INTERVAL"]?>000,
	<?else:?>
	  interval: 30000,
	<?endif;?>
	<?if($arParams["ES_WITDH_AUTO"]=="Y"):?>
	  width: 'auto',
	<?else:?>
	  width: <?=$arParams["ES_WITDH"]?>,
	<?endif;?>
	  height: <?=$arParams["ES_HEIGHT"]?>,
	  theme: {
		shell: {
		  background: '#<?=$arParams["ES_SHELL_BACKGROUND"]?>',
		  color: '#<?=$arParams["ES_SHELL_COLOR"]?>'
		},
		tweets: {
		  background: '#<?=$arParams["ES_TWEETS_BACKGROUND"]?>',
		  color: '#<?=$arParams["ES_TWEETS_COLOR"]?>',
		  links: '#<?=$arParams["ES_TWEETS_LINKS"]?>'
		}
	  },
	  features: {
		scrollbar: <?=$arParams["ES_SCROLL"]=="Y" ? "true" : "false"?>,
	<?if($arParams["ES_BEHAVIOR"]=="default"):?>
		loop: <?=$arParams["ES_LOOP"]=="Y" ? "true" : "false"?>,
	<?else:?>
		loop: false,
	<?endif;?>
		live: <?=$arParams["ES_REFRESH"]=="Y" ? "true" : "false"?>,
		behavior: '<?=$arParams["ES_BEHAVIOR"]?>'
	  }
	<?if($arParams["ES_TYPE"]=="profile" || $arParams["ES_TYPE"]=="faves"):?>
	}).render().setUser('<?=$arParams["ES_TWITTER"]?>').start();
	<?endif;?>
	<?if($arParams["ES_TYPE"]=="search"):?>
	}).render().start();
	<?endif;?>
	<?if($arParams["ES_TYPE"]=="list"):?>
	}).render().setList('<?=$arParams["ES_TWITTER"]?>', '<?=$arParams["ES_LIST"]?>').start();
	<?endif;?>
</script>
<?endif;?>