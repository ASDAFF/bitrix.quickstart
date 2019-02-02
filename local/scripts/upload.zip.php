<?

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog.php");

$APPLICATION->RestartBuffer();


set_time_limit(0);

@mkdir(BASE_PATH . '/temp', 0777);

$filename = 'upload.' . date('Ymd') . '.zip';
$filepath = BASE_PATH . "/temp/$filename";

@unlink($filepath);

//$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(BASE_PATH . '/upload'), RecursiveIteratorIterator::CHILD_FIRST);

$archive = new PclZip($filepath);
$archive->add(BASE_PATH . DIRECTORY_SEPARATOR . 'upload', PCLZIP_OPT_REMOVE_PATH, BASE_PATH);

while (@ob_implicit_flush());

if (file_exists($filepath))
{
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //keeps ie happy
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-type: application/octet-stream");
    header("Content-Length: " .(filesize($filepath)));
    header('Content-Transfer-Encoding: binary');

    chmod($filepath, 0666);

    $handle = @fopen($filepath, "rb");

    fpassthru($handle);

    fclose($handle);
}

unlink($filepath);