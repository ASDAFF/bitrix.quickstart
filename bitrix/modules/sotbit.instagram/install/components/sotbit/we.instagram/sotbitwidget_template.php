<?php
if(!$SotbitWidget) die('inWidget object not init.');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title>Instagram</title>
		<meta http-equiv="content-type" content="text/html; charset=<?=$_GET["charset"]?>" />
		<meta http-equiv="content-language" content="ru" />
		<meta http-equiv="content-style-type" content="text/css2" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<style type='text/css'>
			body {
				color: #212121;
   				font-family: arial;
   				font-size:12px;
   				padding:0px;
   				margin:0px;
			}
			img {
				border: 0;
			}
			.clear {
				clear:both;
				height:1px;
				line-height:1px;
			}
			.widget {
				width:<?php echo $SotbitWidget->width; ?>px;
				border:1px solid #c3c3c3;
				background:#f9f9f9;
				border-radius: 5px 5px 5px 5px;
				-webkit-border-radius: 5px 5px 5px 5px;
				-moz-border-radius: 5px 5px 5px 5px;
				overflow:hidden;
			}
			.widget a.title:link, .widget a.title:visited  {
				display:block;
				height:33px;
				background:#46729b url(i/bg-title.gif) repeat-x;
				text-decoration:none;
			}
				.widget .title .icon{
					display:block;
					float:left;
					width:25px;
					height:25px;
					margin:4px 10px 0 5px;
				}
				.widget .title .text {
					float:left;
					width: <?php echo ($SotbitWidget->width-44); ?>px;
					height:25px;
					overflow:hidden;
					margin:5px 0 0 0;
					color:#FFF;
					font-size:18px;
					white-space:nowrap;
					<?php if($SotbitWidget->width<130) echo 'display:none'; ?>
				}
			.widget .profile {
				width:100%;
				border-collapse: collapse;
			}
				.widget .profile tr td {
					padding:0px;
					margin:0px;
					text-align:center;
				}
				.widget .profile td {
					border:1px solid #c3c3c3;
				}
				.widget .profile .avatar {
					width:1%;
					padding:10px !important;
					border-left:none !important;
					line-height:0px;
				}
					.widget .profile .avatar img {
						width:60px;
					}
				.widget .profile .value {
					width:33%;
					height:30px;
					font-size:14px;
					font-weight:bold;
				}
				.widget .profile span {
					display:block;
					font-size:9px;
					font-weight:bold;
					color:#999999;
					margin:-2px 0 0 0;
				}
			.widget .data{
				text-align:left;
				margin:10px 0 0 10px;
				padding:0 0 5px 0;
			}
				.widget .data .image {
					display:block;
					float:left;
					margin:0 5px 5px 0;
					width:<?php echo $SotbitWidget->imgWidth; ?>px;
					height:<?php echo $SotbitWidget->imgWidth; ?>px;
					overflow:hidden;
					border:2px solid #FFF;
					box-shadow: 0 1px 1px rgba(0,0,0,0.3);
					ling-height:0px;
					
				}
					.widget .data .image img{
						width:<?php echo $SotbitWidget->imgWidth; ?>px;
					}
				.widget .data .image:hover {
					filter: alpha(opacity=80);
    				opacity: 0.8;
				}
			.widget a.follow:link, .widget a.follow:visited {
				display:block;
				background:#ad4141;
				text-decoration:none;
				font-size:14px;
				color:#FFF;
				font-weight:bold;
				width:130px;
				margin:0 auto 0 auto;
				padding:4px 4px 4px 10px;
				border:3px solid #FFF;
				border-radius: 5px 5px 5px 5px;
				-webkit-border-radius: 5px 5px 5px 5px;
				-moz-border-radius: 5px 5px 5px 5px;
				box-shadow: 0 0px 2px rgba(0,0,0,0.5);
			}
			.widget a.follow:hover {
				background:#cf3838;
			}
		</style>
	</head>
<body>
<div class='widget'>
	<?php 
		// выводим заголовок
        //printr($SotbitWidget);
		echo '
		<a href="http://instagram.com/'.$SotbitWidget->profile['username'].'" target="_blank" class="title">
			<img src="/bitrix/components/sotbit/we.instagram/images/icon.png" class="icon" />
			<div class="text">'.$SotbitWidget->title.'</div>
			<div class="clear">&nbsp;</div>
		</a>';
		// выводим тулбар
		if($SotbitWidget->toolbar == true) {
			echo '
			<table class="profile">
				<tr>
					<td rowspan="2" class="avatar">
						<a href="http://instagram.com/'.$SotbitWidget->profile['username'].'" target="_blank"><img src="'.$SotbitWidget->profile['avatar'].'"></a>
					</td>
					<td class="value">
						'.$SotbitWidget->profile['posts'].'
						<span>posts</span>
					</td>
					<td class="value">
						'.$SotbitWidget->profile['followers'].'
						<span>followers</span>
					</td>
					<td class="value" style="border-right:none !important;">
						'.$SotbitWidget->profile['following'].'
						<span>following</span>
					</td>
				</tr>
				<tr>
					<td colspan="3" style="border-right:none !important;">
						<a href="http://instagram.com/'.$SotbitWidget->profile['username'].'" class="follow" target="_blank">'.$SotbitWidget->show.' &#9658;</a>
					</td>
				</tr>
			</table>';
		}
		if(!empty($SotbitWidget->data)){
			shuffle($SotbitWidget->data);
			$SotbitWidget->data = array_slice($SotbitWidget->data,0,$SotbitWidget->view);
			echo '<div id="widgetData" class="data">';
			foreach ($SotbitWidget->data as $key=>$item){
				switch ($SotbitWidget->preview){
					case 'large':
						$thumbnail = $item["images"]["low_resolution"]["url"];
						break;
					case 'fullsize':
						$thumbnail = $item["images"]["standard_resolution"]["url"];
						break;
					default:
						$thumbnail = $item["images"]["thumbnail"]["url"];
				}
				echo '<a href="'.$item["link"].'" class="image" target="_blank"><img src="'.$thumbnail.'" /></a>';
			}
			echo '<div class="clear">&nbsp;</div>';
			echo '</div>';
		}
	?>
</div>
</body>
</html>
<!-- 
	Inwidget - small PHP script showing images from instagram.com in you site!
	http://inwidget.ru
	© Alexandr Kazarmshchikov
-->