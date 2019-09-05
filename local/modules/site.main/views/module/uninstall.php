<!DOCTYPE html>
<html>
	<head>
		<title><?=$result['title']?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?=SITE_CHARSET?>"/>
		<style type="text/css">
			body {
				text-align: center;
			}
		</style>
	</head>
	<body>
		<h1><?=$result['question']?></h1>
		<form action="" method="POST">
			<input type="hidden" name="confirm" value="Y"/>
			<input type="submit" value="<?=$result['yes']?>" disabled="disabled"/>
			<input type="button" value="<?=$result['no']?>" onclick="document.location.href='<?=$GLOBALS['APPLICATION']->GetCurPage()?>'"/>
		</form>
	</body>
</html>