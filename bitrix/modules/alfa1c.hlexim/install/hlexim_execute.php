<?
ini_set("max_execution_time","0");
ini_set("allow_url_fopen","1");
//We need it, sry :(

if($_REQUEST['action'] == "send"):

$url = "http://optiimg.tk/conv/";
$ht = isset($_SERVER['HTTP_SCHEME']) ? $_SERVER['HTTP_SCHEME'] : (
     (
  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
   443 == $_SERVER['SERVER_PORT']
     ) ? 'https://' : 'http://'
 
 );

$post_data = array (
    "key" => $_REQUEST['api_key'],
	"png_quality" => $_REQUEST['png_quality'],
	"jpg_quality" => $_REQUEST['jpg_quality'],
    "src" => $ht.$_REQUEST['src']
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// указываем, что у нас POST запрос
curl_setopt($ch, CURLOPT_POST, 1);
// добавляем переменные
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

$output = curl_exec($ch);

curl_close($ch);

echo $output;
elseif($_REQUEST['action'] == 'save'):
	if(ini_get('allow_url_fopen')==0){
		$res['status'] = 'error';
		$res['error_text'] = 'allow_url_fopen disabled';
		echo json_encode($res);
		die();
	}
	$blacklist = array(".php", ".phtml", ".php3", ".php4");
	foreach ($blacklist as $item) {
	  if(preg_match("/$item\$/i", basename($_REQUEST['src']))) {
	  	 echo "success\n";
	   exit;
	   }
	  }
	$img = file_get_contents($_REQUEST['src']);
	if(!$img){
		$res['status'] = 'error';
		$res['error_text'] = 'no data fetched';
		echo json_encode($res);
		die();
	}
	$src = file_put_contents($_SERVER["DOCUMENT_ROOT"].'/'.$_REQUEST['src_old'], $img);
endif;
?>