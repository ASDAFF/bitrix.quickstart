<?
$dir = dirname($_SERVER['REQUEST_URI']) . '/';

switch ($_GET['mode']) {
	case 'frame':
		header('Content-type: text/html; charset=windows-1251');
		?>
		<html>
			<head>
				<title>No Old Browser</title>
				<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
				<link rel="stylesheet" type="text/css" href="<?=$dir?>panel.css"/>
			</head>
			<body class="no-old-browser">
				<a class="no-old-browser-close" href="javascript:hideSelf();">&nbsp;</a>
				<div class="no-old-browser-video">Что такое браузер?<br /><a href="http://www.whatbrowser.org/ru/" target="_blank">Посмотреть видео</a></div>
				<div class="no-old-browser-title">Ваш браузер слишком стар для этого сайта. Мы рекомендуем вам установить новый браузер.</div>
				<table class="no-old-browser-icons">
					<tr>
						<td><a href="http://www.google.com/chrome?hl=ru" target="_blank"><span class="no-old-browser-icon no-old-browser-chrome"></span>Google Chrome</a></td>
						<td><a href="http://www.mozilla.org/ru/firefox/new/" target="_blank"><span class="no-old-browser-icon no-old-browser-firefox"></span>Mozilla Firefox</a></td>
						<td><a href="http://ru.opera.com/" target="_blank"><span class="no-old-browser-icon no-old-browser-opera"></span>Opera</a></td>
						<td><a href="http://www.microsoft.com/rus/windows/internet-explorer/" target="_blank"><span class="no-old-browser-icon no-old-browser-ie"></span>Internet Explorer</a></td>
					</tr>
				</table>
				<script type="text/javascript">
					function hideSelf() {
						top.document.getElementById('no-old-browser-frame').style.display = 'none';
					}
				</script>
			</body>
		</html>
		<?
		break;
	
	default:
		header('Content-type: text/javascript');
		?>
		var body = document.getElementsByTagName('body')[0];
		
		var frame = document.createElement('iframe');
		frame.id = 'no-old-browser-frame';
		frame.src = '<?=$dir?>panel.php?mode=frame';
		body.insertBefore(frame, body.firstChild);
		
		document.write('<link rel="stylesheet" type="text/css" href="<?=$dir?>panel.css"/>');
		<?
}
?>