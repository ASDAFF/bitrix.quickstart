<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Стартовый шаблон Front-end</title>

    <link href="css/vendors.min.css" rel="stylesheet">
    <link href="css/base.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php echo "<h1>Тестовая страница PHP</h1>"?>
			</div>			
		</div>
		<div class="row">
			<?php for ($i=1; $i <= 12; $i++) :?>
				<div class="col-md-1"><?=$i?></div>
			<?php endfor;?>
		</div>
		<div class="row">
			<div class="col-md-12">
				<?php phpinfo();?>
			</div>			
		</div>
	</div>

	<script defer src="js/vendors.min.js"></script>
</body>
</html>