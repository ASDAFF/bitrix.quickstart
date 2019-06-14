<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(function_exists('yenisite_GetCompositeLoader')){global $MESS;$MESS ['COMPOSITE_LOADING'] = yenisite_GetCompositeLoader();}?>

<?if(method_exists($this, 'createFrame')) $frame = $this->createFrame()->begin(GetMessage('COMPOSITE_LOADING'));?>

<?
		$result = '';
 
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) {
			$result .= 'https://';
		} else {
			$result .= 'http://';
		}
		
		$result .= $_SERVER['SERVER_NAME'];
		$result .= $_SERVER['REQUEST_URI'];	
?>

<div class="fb-comments" 
	 data-href=<?=$result;?> 
	 data-numposts=<?=$arParams["NUMPOSTS"];?> 
	 data-colorscheme=<?=$arParams["COLORSCHEME"];?>
	 data-order-by=<?=$arParams["ORDER_BY"];?>
	 data-width=<?=$arParams["WIDTH"];?>>	 
</div>

<?if(method_exists($this, 'createFrame')) $frame->end();?>