<?
$file = fopen($_SERVER['DOCUMENT_ROOT'].$_GET['file'],"r");
//print $_GET['test'];
if($file){
	$exts = explode(".",$_GET['file']);
	$ext = $exts[sizeof($exts)-1];
	//print "Content-type: file/".$ext;
	header("Content-type: file/".$ext);
	while($str = fgets($file,4096)) print $str;
}
?>