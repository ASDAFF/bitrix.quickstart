<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $USER;
if (!$USER->IsAuthorized() && !strpos($_SERVER['REQUEST_URI'], "upload/") && !strpos($_SERVER['REQUEST_URI'], ".txt")){
	$requr = explode("?", $_SERVER['REQUEST_URI']);
	$requr_without_get=$requr[0];
	$url='http://' .$_SERVER['SERVER_NAME'] .$requr_without_get;
	$lastmod = date('Y-m-d', time());
	$url = htmlspecialchars($url);
	$pattern_url=$url .'</loc>';
	$smp=$_SERVER['DOCUMENT_ROOT'] .'/'.$arParams['FILENAME'];
	$rz = @file_get_contents($smp);
	$len=strlen($rz);
	if($len==0){

		$rz='<?xml version="1.0" encoding="UTF-8"?>
		<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
		<url>
					<loc>'.$url.'</loc>
					<lastmod>'.$lastmod .'</lastmod>
					<priority>'.$arParams['PRIORITY'].'</priority>
		</url>
		</urlset>';
		
		file_put_contents ($smp,$rz);

	}
	if(!strstr($rz,$pattern_url))
	{
	$replace=preg_replace("'(.*)<urlset'si","<urlset",$rz);
	$replace=preg_replace("'>(.*)'si",">",$replace);
	$shablon='<url>
					<loc>'.$url.'</loc>
					<lastmod>'.$lastmod .'</lastmod>
					<priority>'.$arParams['PRIORITY'].'</priority>
		</url>';
	$rz = str_replace($replace,$replace .'
	' .$shablon,$rz);

	file_put_contents ($smp,$rz);

	}
}

?>


