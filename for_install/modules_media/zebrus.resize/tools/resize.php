<?php
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NOT_CHECK_PERMISSIONS", true);
$HTTP_ACCEPT_ENCODING = "";
$_SERVER["HTTP_ACCEPT_ENCODING"] = "";


// определяем путь к корневой папке
$DOCUMENT_ROOT = getenv("DOCUMENT_ROOT"); 


// принемаем GET параметры
$w = $_GET['w']; // Число пикселей. Указываем, когда необходимо масштабирование по ширине. от 10 до 2000
$wr = $_GET['wr']; // Число пикселей. Оставить область в центре по горизонтали. 
$h = $_GET['h']; // Число пикселей. Указываем, когда необходимо масштабирование по высоте. от 10 до 2000
$hr = $_GET['hr']; // Число пикселей. Оставить область в центре по вертикали.
$q = $_GET['q']; // Качество jpeg компрессии генерируемой превью. от 10 до 100. По умолчанию 80
$file = $_GET['file']; // собираем реальный путь к изображению
$file = '/'.$file;

$DobleResize	= 'no'; // Уменьшать большие фото (в 3 раза больше желаемого) с шагом 50% для улучшения качества? (yes/no)

$CopyResampled	= 'yes'; // Использовать функцию imageCopyResampled (бикубическое масштабирование) для улучшения качества? (yes/no)
// Внимание! При включении расходуется больше памяти и времени, поэтому при превышении лимитов работа скрипта может быть принудительно прервана сервером.

$LocalCache	= $DOCUMENT_ROOT.'/bitrix/cache/zebrus_resize/'; // Путь к папке с кэшем (если пусто, не кэшируется). 

$CacheTime	= 24*7; // Хранить в кэше часов (если 0, не удалять из кэша старые файлы)

$Directory	= $DOCUMENT_ROOT; // Опция для профи и требовательных к безопасности.
// Локальный путь, относительно которого находятся файлы изображений. По умолчанию - корень сайта.
// Пример: $Directory=$DOCUMENT_ROOT.'/images/';
// Тогда в url достаточно указывать имя файла: http://yoursite.ru/resize/85x0x85x0x80/img.jpg


$Original	= 'yes'; // По умолчанию, если размеры изображения меньше, чем необходимые для генерации превью, будет показан оригинал без уменьшения. 
// Если указать 'no' то изображение будет  увеличиваться до указанного размера

$MaxSize	= 25; // Максимальный размер фото в Мегапикселах. Если фото больше этого размера, то оно не будет уменьшаться и появится сообщение с ошибкой. 
// Необходимо в тех случаях, когда установлен лимит на выделяемую память php скрипту или есть другие внутренние ограничения.

$max_w = 2000; // Максимальный размер по ширине, если значение будет  указано больше, то применется максимальное
$max_h = 2000; // Максимальный размер по высоте, если значение будет  указано больше, то применется максимальное


/////////////////////////////////////////////////////////////////

// Максимальная ширина
$w=(int)$w;
if ($w>0 && $w<10) $w=10;
if ($w>$max_w) $w=$max_w;

// Максимальная высота
$h=(int)$h;
if ($h>0 && $h<10) $h=10;
if ($h>2000) $h=2000;

if($w==0 && $h==0){
error_gif('Parameters - W=0 and H=0');
}

// Оставить область в центре по горизонтали (отрезать лишнее)
$wr=(int)$wr;

// Оставить область в центре по вертикали (отрезать лишнее)
$hr=(int)$hr;

// Качество картинки
$q=(int)$q;
if ($q<10 || $q>100) $q=80;

// Путь к файлу относительно корня сайта
$file=str_replace('\\', '/', $file);

if (
	$file=='' ||
	mb_ereg('\.\.|//', $file) ||
	!mb_ereg('^[-_!./a-zA-Z0-9]+$', $file) ||
	!mb_ereg('[a-zA-Z0-9]$', $file) ||
	(mb_ereg('/$', $Directory) && mb_ereg('^/', $file)) ||
	(!mb_ereg('/$', $Directory) && !mb_ereg('^/', $file))
   ) {
	error_gif('Incorrect variable file.');
}


$file_local=$Directory.$file;



$file_name=md5($file_local.'-'.$z.$m.'-'.$w.$wr.'-'.$h.$hr.'-'.$p.$q.$CopyResampled.$DobleResize);

$flag_ok=1;

// Есть ли в кэше?
if ($LocalCache && file_exists($LocalCache.$file_name)) {

	$flag=1;
	$ftime=@filemtime($LocalCache.$file_name);

	if (rand(0,20)==1 && $ftime) {
		// Файл есть в кеше.
		// Случайно 1 из 20 раз проверяем - есть ли оригинальный файл и не изменился ли он
		$orig_ftime=@filemtime($DOCUMENT_ROOT.$file);
		if (!$orig_ftime || $orig_ftime>$ftime) {
			// Оригинального файла нет или его дата свежее
			// Удаляем превью
			@unlink($LocalCache.$file_name);
			$flag=0;
		}
	}

	if ($flag) {
		// Файл есть в кэше. Показываем его как JPG (в кеше только JPG)
		header_img(2, @filesize($LocalCache.$file_name), $ftime, substr($file_name,0,20).'.jpg');
		if (function_exists('file_get_contents')) {
			echo file_get_contents($LocalCache.$file_name);
		} else {
			@readfile($LocalCache.$file_name);
		}

		// Чистим кэш от старых файлов (не каждый раз, а случайно)
		if ($CacheTime>0 && rand(0, $CacheTime/5+1)==1) {
			$d=opendir($LocalCache);
			while (($e=readdir($d))!=false) {
				if (!mb_ereg('\.', $e)) {
					$ft=@filemtime($LocalCache.$e);
					if ($ft && $ft+3600*$CacheTime < time()) @unlink($LocalCache.$e);
				}
			}
			closedir($d);
		}
		exit;
	}

} 

if (!file_exists($file_local)) error_gif('File not found.');

// Узнаём размеры и формат
$imSize=@GetImageSize($file_local);

if (!$imSize) error_gif('To specify a file format JPG, GIF or PNG.');

if (
	($w>0 && $imSize[0]<=$w) ||
	($h>0 && $imSize[1]<=$h)
	) {
	// Размеры оригинальной картинки меньше необходимого.

	if ($Original=='yes') {
		// Тупо показываем и выходим :)

		header_img($imSize[2]);
		if (function_exists('file_get_contents')) {
			echo file_get_contents($file_local);
		} else {
			@readfile($file_local);
		}
	exit;
	} 


} else if ($imSize[0]*$imSize[1] > $MaxSize*1024000) {
	error_gif('Photo is too large. Maximum size - '.$MaxSize.'  megapixels.');
}

if ($flag_ok==0) error_gif('Sorry, no preview generated by the administrator.');


// Открываем файл
if ($imSize[2]==1) {
	$im=@imageCreateFromGif($file_local);
} else if ($imSize[2]==2) {
	$im=@imageCreateFromJpeg($file_local);
} else if ($imSize[2]==3) {
	$im=@imageCreateFromPng($file_local);
}
if (!$im) error_gif('Could not open the file.');

// Определяем максимальную длину по широкой стороне
if ($imSize[0]>$imSize[1]) {
	$Max=$imSize[0];	// X
} else {
	$Max=$imSize[1];	// Y
}



// Определяем конечные размеры
 if ($w>0 && $h>0) {
	// Оптимальный режим
	$TestY=(int)(($imSize[1]/$imSize[0])*$w);
	if ($TestY<$h) {
		$EndY=$h;
		$EndX=(int)(($imSize[0]/$imSize[1])*$h);
	} else {
		$EndX=$w;
		$EndY=$TestY;
	}
	if (!$hr) $hr=$h;
	if (!$wr) $wr=$w;

} else if ($w>0) {
	$EndX=$w;
	$EndY=(int)(($imSize[1]/$imSize[0])*$w);
} else {
	$EndY=$h;
	$EndX=(int)(($imSize[0]/$imSize[1])*$h);
}

//////////// ПЕРВЫЙ ШАГ ////////////////////////

$DobleResize_flag=0;
if (
	$DobleResize=='yes' &&
	     (
		($w>0 && $imSize[0]/3>$w) ||
		($h>0 && $imSize[1]/3>$h)
	     )
	) {
	// Фото огромное, поэтому уменьшаем в 2 шага
	$DobleResize_flag=1;

	// Готовимся уменьшить в 2 раза
	$NewX=(int)($imSize[0]/2);
	$NewY=(int)($imSize[1]/2);

	// Создаём новый
	$imNew = imagecreatetruecolor($NewX, $NewY);

	// Уменьшаем без сглаживания - грубо, но быстро
	imageCopyResized($imNew, $im, 0, 0, 0, 0, $NewX, $NewY, $imSize[0], $imSize[1]);

	// Удаляем из памяти оригинал
	ImageDestroy ($im);
}

//////////// ВТОРОЙ ШАГ ///////////////////////

// Создаём новый
$imEnd = imagecreatetruecolor($EndX, $EndY);

///////////У PNG заменяем прозрачность на белый цвет, т.к. превью отдается в jpg //////////////////////////////
$tr = imagecolorallocate($imEnd, 255, 255, 255); 
// заливка изображения белым 
imagefill($imEnd, 0, 0, $tr); 


// Уменьшаем
if ($DobleResize_flag) {
	// Был первый шаг
	if ($CopyResampled=='yes') {
		imageCopyResampled($imEnd, $imNew, 0, 0, 0, 0, $EndX, $EndY, $NewX, $NewY);
	} else {
		// Уменьшаем без сглаживания - грубо, но быстро
		imageCopyResized($imEnd, $imNew, 0, 0, 0, 0, $EndX, $EndY, $NewX, $NewY);
	}

	// Удаляем из памяти оригинал
	ImageDestroy ($imNew);
} else {
	if ($CopyResampled=='yes') {
		imageCopyResampled($imEnd, $im, 0, 0, 0, 0, $EndX, $EndY, $imSize[0], $imSize[1]);
	} else {
		imageCopyResized($imEnd, $im, 0, 0, 0, 0, $EndX, $EndY, $imSize[0], $imSize[1]);
	}

	// Удаляем из памяти оригинал
	ImageDestroy ($im);
}

if ($hr && $EndY>$hr) {
	// Отрезаем лишнее по вертикали
	$im = imagecreatetruecolor($EndX, $hr);
	imageCopy($im, $imEnd, 0, 0, 0, (int)(($EndY-$hr)/2), $EndX, $hr);
	$imEnd=$im;
	$EndY=$hr;
}
if ($wr && $EndX>$wr) {
	// Отрезаем лишнее по горизонтали
	$im = imagecreatetruecolor($wr, $EndY);
	imageCopy($im, $imEnd, 0, 0, (int)(($EndX-$wr)/2), 0, $wr, $EndY);
	$imEnd=$im;
	$EndX=$wr;
}

// Показываем созданную превьюху как JPG
header_img(2, 0, time(), substr($file_name,0,20).'.jpg');
ImageJpeg ($imEnd, '', $q);

// Кэшируем
if ($LocalCache) @ImageJpeg ($imEnd, $LocalCache.$file_name, $q);

exit;

// Показываем картинку с текстом ошибки
function error_gif($t) {

	$vsize=46;

	$t1=substr($t, 0, 30);
	if ($t != $t1) {
		$t1=preg_replace(' [^ ]*$', '', $t1);
		$t2=trim(substr($t, strlen($t1), 90));
		$vsize=64;
	}

	header('HTTP/1.1 404 Not Found');
	header_img(1);

	$coin = imagecreate (250, $vsize);

	$color2 = imagecolorallocate($coin, 255, 0, 0);
	$color3 = imagecolorallocate($coin, 255, 255, 255);
	$color4 = imagecolorallocate($coin, 0, 0, 0);

	imagefilledrectangle($coin, 1, 1, 248, $vsize-2, $color3);


	imagestring($coin, 5, 5, 2, 'Error!', $color2);


	imagestring($coin, 4, 5, 20, $t1, $color4);
	if ($t2) imagestring($coin, 4, 5, 20, $t2, $color4);


	ImageGif ($coin); 
	ImageDestroy ($coin);
	exit;
}

function header_img($i, $size=0, $last_modified=0, $file_name='') {

	if ($file_name || $last_modified) {

		if ($last_modified) $last_modified=gmdate('D, d M Y H:i:s', $last_modified);

		if( !function_exists("apache_request_headers") ) {
			function apache_request_headers() {
				$arh = array();
				$rx_http = '/\AHTTP_/';
				foreach ($_SERVER AS $key => $val) {
					if ( preg_match($rx_http, $key) ) {
						$arh_key = preg_replace($rx_http, '', $key);
						$rx_matches = array();
						// do some nasty string manipulations to restore the original letter case
						// this should work in most cases
						$rx_matches = explode('_', $arh_key);
						if ( count($rx_matches) > 0 AND strlen($arh_key) > 2 ) {
							foreach ($rx_matches AS $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
							$arh_key = implode('-', $rx_matches);
						}
						$arh[$arh_key] = $val;
					}
				}
			return( $arh );
			}
		}

		$request_headers = apache_request_headers();

		foreach ($request_headers AS $key=>$value) {
		    if ( preg_match("/^If-Modified-Since$/is", $key) ) {
				if ( strtotime($value)>=strtotime($last_modified) ) {
					header($_SERVER['SERVER_PROTOCOL']." 304 Not Modified");
					exit; 
				}
			}
		}
	}

	if ($i==1) {
		header ("Content-type: image/gif");
	} else if ($i==2) {
		header ("Content-type: image/jpeg");
	} else if ($i==3) {
		header ("Content-type: image/png");
	}
	if ($last_modified) header('Last-Modified: '.$last_modified);
	header('Content-Transfer-Encoding: binary');
	if ($file_name) header('Content-Disposition: inline; filename="'.urldecode($file_name).'"');
	if ($size>0) {
		header('Content-Length: '.$size);
		header('Connection: close');
	}
}


?>
