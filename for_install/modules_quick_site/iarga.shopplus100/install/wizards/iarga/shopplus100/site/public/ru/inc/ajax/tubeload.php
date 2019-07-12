<?
function videolink($inp){
	if(preg_match("#youtu.be/([0-9a-zA-Z\-_]+)#",$inp,$mat))  $inp = $mat[1];
	elseif(preg_match("#youtube.com/embed/([0-9a-zA-Z\-_]+)#",$inp,$mat))  $inp = $mat[1];
	elseif(preg_match("#\?v=#",$inp,$mat)){
		$arr = explode("?v=",$inp);
		if(sizeof($arr)>=2){
			$inp = preg_replace("#&.*#","",$arr[1]);
		}
	}elseif(preg_match("#vimeo.com/([0-9a-zA-Z\-_])+#",$inp,$mat))  $inp = $mat[1];
	
	return $inp;
}
?>
<iframe width="<?=$_GET['w']?>" height="<?=$_GET['h']?>" src="http://www.youtube.com/embed/<?=videolink($_GET['q'])?>?rel=0" frameborder="0" allowfullscreen ></iframe>