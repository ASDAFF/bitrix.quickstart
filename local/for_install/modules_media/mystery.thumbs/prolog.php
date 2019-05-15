<?
define('MYSTERY_THUMBS_URLREWRITER_CONDITION', '#^/thumb/#');
define('MYSTERY_THUMBS_URLREWRITER_FILE_PATH', '/mystery_thumbs.php');

define('MYSTERY_THUMBS_JPG_QUALITY', COption::GetOptionString ( 'mystery.thumbs', 'JPG_QUALITY' ));
define('MYSTERY_THUMBS_BACKGROUND_COLOR', COption::GetOptionString ( 'mystery.thumbs', 'BACKGROUND_COLOR' ));
define('MYSTERY_THUMBS_PNG_TRANSPARENT', COption::GetOptionString ( 'mystery.thumbs', 'PNG_TRANSPARENT' ));
define('MYSTERY_THUMBS_WATERMARK_ENABLE', COption::GetOptionString ( 'mystery.thumbs', 'WATERMARK_ENABLE' ));
define('MYSTERY_THUMBS_WATERMARK_POSITION', COption::GetOptionString ( 'mystery.thumbs', 'WATERMARK_POSITION' ));
define('MYSTERY_THUMBS_WATERMARK_MIN_WIDTH_PICTURE', COption::GetOptionString ( 'mystery.thumbs', 'WATERMARK_MIN_WIDTH_PICTURE' ));
define('MYSTERY_THUMBS_WATERMARK_EXCEPTION', COption::GetOptionString ( 'mystery.thumbs', 'WATERMARK_EXCEPTION' ));
define('MYSTERY_THUMBS_WATERMARK_IMG', COption::GetOptionString ( 'mystery.thumbs', 'WATERMARK_IMG' ));

define('MYSTERY_THUMBS_CHACHE_IMG_PATH', '/upload/resize_cache/mystery.thumbs/');
define('MYSTERY_THUMBS_CHACHE_IMG_SAVE', '/thumb/');
CheckDirPath ( MYSTERY_THUMBS_CHACHE_IMG_PATH );
CheckDirPath ( MYSTERY_THUMBS_CHACHE_IMG_SAVE );

IncludeModuleLangFile ( __FILE__ );
?>