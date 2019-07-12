<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult)) return "";

$strReturn = '';
for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if($arResult[$index]["LINK"] <> "")
	{
		 $strReturn .= '<h1><a href="'.$arResult[$index]["LINK"].'">';			 
		 if($itemSize == $index+1 && $itemSize > 1)
			  $strReturn .= '<span>'.$title.'</span></a></h1>';
		 elseif($itemSize > 1) 
			  $strReturn .= $title.'</a><span>|</span></h1>';
		 else
			  $strReturn .= $title.'</a></h1>';
	}	
	else
	{
		$strReturn .= '<h1><a href="#">';
		 if($itemSize == $index+1 && $itemSize > 1)
			  $strReturn .= '<span>'.$title.'</span></a></h1>';
		 elseif($itemSize > 1) 
			  $strReturn .= $title.'</a><span>|</span></h1>';
		 else
			  $strReturn .= $title.'</a></h1>';		
	}   
}
	
return $strReturn;
?>