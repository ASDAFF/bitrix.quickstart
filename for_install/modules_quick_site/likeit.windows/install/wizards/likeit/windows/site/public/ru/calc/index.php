<?
/**
 * Copyright (c) 3/12/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Калькулятор окон");
?>
<div class="span12">
<?
	define("WINDOWS_IB", '#WINDOWS_IBLOCK_ID#');
	define("OPTION_IB", '#OPTION_IBLOCK_ID#');
	CModule::IncludeModule("iblock");

	if($_REQUEST["step"] == "1" || !isset($_REQUEST["step"])) :
	$dbType = CIBlockSection::GetList(array("SORT" => "ASC", "ID" => "ASC"), array("IBLOCK_ID" => WINDOWS_IB), false, array("ID", "NAME"));
	$types = array();
	while($arType = $dbType->GetNext())
	{
		$types[$arType["ID"]]["NAME"] = $arType["NAME"];
	}
	$dbItems = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), array("IBLOCK_ID" => WINDOWS_IB), false, false, array("ID", "NAME", "CODE", "IBLOCK_SECTION_ID", "PREVIEW_PICTURE"));
	while($arItems = $dbItems->GetNext())
	{
		$types[$arItems["IBLOCK_SECTION_ID"]]["ITEMS"][$arItems["ID"]] = array("NAME" => $arItems["NAME"], "PICTURE" => $arItems["PREVIEW_PICTURE"], "CODE" => $arItems["CODE"]);
	}
?>
	<h2>Выберите вид окна</h2>
<ul class="breadcrumb breadcrumb__t"><li class="active">Выбор окна</li><li class="divider"> | </li><li><a href="javascript:void(0)">Выбор размеров</a></li> <li class="divider"> | </li> <li><a href="javascript:void(0)">Результат</a></li></ul>
	<div id="d_windows">
		<ul id="level-1" class="level1">
			<?
				$bFirst = true;
				foreach($types as $id => $type):?>
				<li <?if($bFirst):?>class="on"<?endif;?> rel="<?=$id?>"><?=$type["NAME"]?></li>
				<?$bFirst = false;
					endforeach;?>
		</ul>
		<?foreach($types as $id => $type):?>
			<div class="div-tab-on" id="tab-<?=$id?>">
				<div class="tab-block">
					<ul class="level2">
						<?  $bFirst = true;
							foreach($type["ITEMS"] as $tid => $item):?>
							<li <?if($bFirst):?>class="on"<?endif;?> rel="<?=$item["CODE"]?>"><?=$item["NAME"]?></li>
							<?$bFirst = false; endforeach;?>
					</ul>
					<?  $bFirst = true;
						foreach($type["ITEMS"] as $tid => $item):?>
						<div id="pic-<?=$item["CODE"]?>" class="div-tab2" <?if($bFirst):?>style="display:block;"<?endif;?>>
							<div rel="<?=$item["CODE"]?>" class="window-item">
								<img src="<?=CFile::GetPath($item["PICTURE"])?>" alt="<?=$item["NAME"]?>" style=""><br>
							</div>
						</div>
						<?$bFirst = false; endforeach;?>
				</div>
			</div>
		<?endforeach;?>
</div>
<div class="next span2">Далее</div>
<script type="text/javascript">
	/*<!--*/
	$('#level-1 li').click( function() {
		$('#level-1 li').removeClass('on');
		$(this).addClass('on');
		$('.div-tab-on').hide();
		$(".level2 li").removeClass('on');
		$(".level2 li:first-child").addClass('on');
		$('.level2').next().show();
		$('#tab-'+$(this).attr('rel')).show();
	});

	$('.level2 li').click( function() {
		$('.level2 li').removeClass('on');
		$(this).addClass('on');
		$('.div-tab2').hide();
		$('#pic-'+$(this).attr('rel')).show();
	});

	$('.next').click( function() {
		parent.location = '<?=SITE_DIR?>calc/?step=2&type='+$("ul.level2 li.on").attr('rel');
	});
	/*-->*/
</script>
<?elseif($_REQUEST["step"] == "2"):?>
<?

	$OPTIONS = array();
	$dbOtionsDir = CIBlockSection::GetList(array(), array("IBLOCK_ID" => OPTION_IB, "CODE" => "options"), false, array("ID"));
	if($arOtionsDir = $dbOtionsDir->GetNext()){
	$dbOptions = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), array("IBLOCK_ID" => OPTION_IB, "SECTION_ID" => $arOtionsDir["ID"]), false, false, array("ID", "CODE", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PROPERTY_PRICE"));
	while($arOption = $dbOptions->GetNext()){
		$OPTIONS[$arOption["ID"]] = $arOption["NAME"];
	}
	}

	$bDoor = false;
	if((in_array("DC", explode("-", $_REQUEST["type"])))) $bDoor = true;
	if($bDoor):
	$dbItems = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), array("IBLOCK_ID" => WINDOWS_IB, "CODE" => $_REQUEST["type"]), false, false, array("ID", "CODE", "NAME", "IBLOCK_SECTION_ID", "PREVIEW_PICTURE", "PROPERTY_HEIGHT", "PROPERTY_WIDTH", "PROPERTY_WINDOW"));
	if($arDoor = $dbItems->GetNext())
	{
			$_height = explode("-", $arDoor["PROPERTY_HEIGHT_VALUE"]);
			$arDoor["PROPERTY_HEIGHT_VALUE"] = array();
			$arDoor["PROPERTY_HEIGHT_VALUE"]["MIN"] = $_height[0];
			$arDoor["PROPERTY_HEIGHT_VALUE"]["MAX"] = $_height[1];

			$_width = explode("-", $arDoor["PROPERTY_WIDTH_VALUE"]);
			$arDoor["PROPERTY_WIDTH_VALUE"] = array();
			$arDoor["PROPERTY_WIDTH_VALUE"]["MIN"] = $_width[0];
			$arDoor["PROPERTY_WIDTH_VALUE"]["MAX"] = $_width[1];


		if(($_REQUEST["type"] != "DC") && intval($arDoor["PROPERTY_WINDOW_VALUE"])){
		$dbWindow = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), array("IBLOCK_ID" => WINDOWS_IB, "ID" => $arDoor["PROPERTY_WINDOW_VALUE"]), false, false, array("ID", "CODE", "NAME", "IBLOCK_SECTION_ID", "PREVIEW_PICTURE", "PROPERTY_HEIGHT", "PROPERTY_WIDTH", "PROPERTY_WINDOW"));
		if($arWindow = $dbWindow->GetNext()){
			$_height = explode("-", $arWindow["PROPERTY_HEIGHT_VALUE"]);
			$arWindow["PROPERTY_HEIGHT_VALUE"] = array();
			$arWindow["PROPERTY_HEIGHT_VALUE"]["MIN"] = $_height[0];
			$arWindow["PROPERTY_HEIGHT_VALUE"]["MAX"] = $_height[1];

			$_width = explode("-", $arWindow["PROPERTY_WIDTH_VALUE"]);

			$arWindow["PROPERTY_WIDTH_VALUE"] = array();
			$arWindow["PROPERTY_WIDTH_VALUE"]["MIN"] = $_width[0];
			$arWindow["PROPERTY_WIDTH_VALUE"]["MAX"] = $_width[1];
		}
		$dbDoor = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), array("IBLOCK_ID" => WINDOWS_IB, "CODE" => "DC"), false, false, array("ID", "PREVIEW_PICTURE"));
		if($_arDoor = $dbDoor->GetNext()){
			$arDoor["PREVIEW_PICTURE"] = $_arDoor["PREVIEW_PICTURE"];
		}
		}
	};
	else:

	$dbItems = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), array("IBLOCK_ID" => WINDOWS_IB, "CODE" => $_REQUEST["type"]), false, false, array("ID", "CODE", "NAME", "IBLOCK_SECTION_ID", "PREVIEW_PICTURE", "PROPERTY_HEIGHT", "PROPERTY_WIDTH", "PROPERTY_WINDOW"));
	if($arWindow = $dbItems->GetNext())
	{
			$_height = explode("-", $arWindow["PROPERTY_HEIGHT_VALUE"]);
			$arWindow["PROPERTY_HEIGHT_VALUE"] = array();
			$arWindow["PROPERTY_HEIGHT_VALUE"]["MIN"] = $_height[0];
			$arWindow["PROPERTY_HEIGHT_VALUE"]["MAX"] = $_height[1];

			$_width = explode("-", $arWindow["PROPERTY_WIDTH_VALUE"]);
			$arWindow["PROPERTY_WIDTH_VALUE"] = array();
			$arWindow["PROPERTY_WIDTH_VALUE"]["MIN"] = $_width[0];
			$arWindow["PROPERTY_WIDTH_VALUE"]["MAX"] = $_width[1];
	}

	endif;
?>
  <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.nouislider.min.js"></script>
  <link rel="stylesheet" type="text/css" media="all" href="<?=SITE_TEMPLATE_PATH?>/css/jquery.nouislider.css"/ />
		<h2>Выберите высоту и ширину</h2>
<ul class="breadcrumb breadcrumb__t"><li><a href="<?=SITE_DIR?>calc/">Выбор окна</a></li><li class="divider"> | </li><li  class="active">Выбор размеров</li> <li class="divider"> | </li> <li><a href="javascript:void(0)">Результат</a></li></ul>
<div id="calc2-table">
<form action="" name="calc" id="form_calc">
		<div class="add">
		<span>Дополнительные параметры</span><br/>
		<span>Тип дома:</span><br/>
		<label><input type="radio" id="type_0" value="1" name="type" checked="checked"/> панель</label>
		<label><input type="radio" name="type" value="2"/> кирпич</label>
		<br/>
		<span>Дополнительные услуги:</span>
		<?foreach($OPTIONS as $id => $option):?>
		<label><input type="checkbox" name="service[]" id="serv_<?=$id?>" value="<?=$id?>"/> <?=$option?></label>
		<?endforeach;?>
		<div class="next span2" onclick="f_calc()" >Рассчитать</div>
	</div>
	<div class="container">
	<?if($arWindow["ID"]):?>
		<div class="cell" style="width:<?if($bDoor):?>55<?else:?>100<?endif;?>%;">
			<div class="cell-cnt" style="<?if($bDoor):?>text-align:right;<?else:?>text-align:center;<?endif;?>">
				<div class="img-block" >
					<img src="<?=CFile::GetPath($arWindow["PREVIEW_PICTURE"])?>" alt="<?=$arWindow["NAME"]?>"/>
					<div id="slider-height" class="noUiSlider" style="height:100%;"></div>
					<span class="height1"><input id="height" name="height" style="color: #1c8dcd; font-weight: bold; text-align:center;" value="<?=$arWindow["PROPERTY_HEIGHT_VALUE"]["MAX"]?>"/><br/>
					Высота окна, мм</span>
					<div id="slider-width" class="noUiSlider" style="width:100%;"></div>
					<span class="width1"><input id="width" name="width" style="color: #1c8dcd; font-weight: bold; text-align:center;" value="<?=$arWindow["PROPERTY_WIDTH_VALUE"]["MIN"]?>"/><br/>Ширина окна, мм</span>
				</div>
			</div>
		</div>
		<script type="text/javascript">
$("#slider-width").noUiSlider({
	range: {
		'min': [ <?=$arWindow["PROPERTY_WIDTH_VALUE"]["MIN"]?> ],
		'max': [ <?=$arWindow["PROPERTY_WIDTH_VALUE"]["MAX"]?> ]
	},
	start: <?=$arWindow["PROPERTY_WIDTH_VALUE"]["MIN"]?>,
	step: 100,
	handles: 1
}).on( {slide: function(){
		$('#width').val($(this).val());
   }});

$("#slider-height").noUiSlider({
	range: {
		'min': [ <?=$arWindow["PROPERTY_HEIGHT_VALUE"]["MIN"]?> ],
		'max': [ <?=$arWindow["PROPERTY_HEIGHT_VALUE"]["MAX"]?> ]
	},
	start:<?=$arWindow["PROPERTY_HEIGHT_VALUE"]["MAX"]?>,
	step:100,
	handles:1,
	direction: "rtl",
	orientation:"vertical",
	connect: false,

}).on({ slide: function(){
		$('#height').val($(this).val());
   }});
		</script>
		<?endif;?>
		<?if($arDoor["ID"]):?>
		<div class="cell" style="border-radius:0px;<?if($arWindow["ID"]):?>width:45%<?else:?>width:100%<?endif;?>">
			<div class="cell-cnt" style="<?if($arWindow["ID"]):?>text-align:left; padding-left:20px;<?else:?>text-align:center;<?endif;?>">
				<div class="img-block">
					<img src="<?=CFile::GetPath($arDoor["PREVIEW_PICTURE"])?>" alt="<?=$arDoor["NAME"]?>"/>
					<div id="slider-d_height" class="noUiSlider" style="height:100%;"></div>
					<span class="height2"><input id="d_height" name="d_height" style="color: #1c8dcd; font-weight: bold; text-align:center;"  value="<?=$arDoor["PROPERTY_HEIGHT_VALUE"]["MAX"]?>"/><br/>Высота двери, мм</span>
					<div id="slider-d_width" class="noUiSlider" style="width:100%;"></div>
					<span class="width2"><input id="d_width" name="d_width" style="color: #1c8dcd; font-weight: bold; text-align:center;" value="<?=$arDoor["PROPERTY_WIDTH_VALUE"]["MIN"]?>"/><br />
					Ширина двери, мм</span>
				</div>
			</div>
		</div>

		<script>
			$("#slider-d_width").noUiSlider({
	range:{
		'min': [ <?=$arDoor["PROPERTY_WIDTH_VALUE"]["MIN"]?> ],
		'max': [ <?=$arDoor["PROPERTY_WIDTH_VALUE"]["MAX"]?> ]
	},
	start: <?=$arDoor["PROPERTY_WIDTH_VALUE"]["MIN"]?>,
	step: 100,
	handles: 1
}).on({ slide: function(){
		$('#d_width').val($(this).val());
   }});

$("#slider-d_height").noUiSlider({
	range: {
		'min': [ <?=$arDoor["PROPERTY_HEIGHT_VALUE"]["MIN"]?> ],
		'max': [ <?=$arDoor["PROPERTY_HEIGHT_VALUE"]["MAX"]?> ]
	},
	start:<?=$arDoor["PROPERTY_HEIGHT_VALUE"]["MAX"]?>,
	step: 100,
	handles: 1,
	connect: false,
	direction: "rtl",
	orientation:"vertical",
}).on('slide',  function(){
		$('#d_height').val($(this).val());
   });
		</script>
		<?endif;?>
</div>
</form>
</div>

<div id="d_loading" class="loading"></div>
<div id="d_result" style="display:none"></div>
<script type="text/javascript">
/*<!--*/
function f_calc() {
	$('#d_loading').show();
	var request = $.ajax({ url: "<?=SITE_DIR?>ajax/form_calc.php?ACDC=<?=$_REQUEST['type']?>&"+$("#form_calc").serialize(),type: "GET", data: { }, dataType: "text"});
	request.done(function(msg) {
		$('#d_loading').hide();
		$('#d_result').empty().html(msg);
		$('#d_result').show();
	});
}
/*-->*/
</script>
<?endif;?>
<style type="">
	#d_windows {
		display:block;
		height:350px;
		border-radius:5px;
		overflow:hidden;
	}


	#d_windows ul.level1{
		display:inline-block;
		float:left;
		padding:0px;
		min-height:350px;
		width:20%;
		min-width:170px;
		margin:0px;
		background:#F7F7F7;
		overflow:hidden;
		margin-bottom:10px;
	}


	#d_windows ul.level1 li {
		margin:0px;
		padding:0px;
		border-bottom:1px solid #F89BAC;
		background:#F7F7F7;
		list-style:none;
		padding:11px 15px 11px;
		margin:0px;
		color:#000000;
		font-size:18px;
		font-weight:bold;
		letter-spacing:-0.5px;
		line-height:25px;
		display:block;
		color:#000000;
		text-decoration:none;
		text-transform:uppercase;
		text-shadow:0px 1px 0px rgba(255,255,255,0.3);
		cursor:pointer;
	}

	.next{
		text-transform:uppercase;
		text-align:center;
		z-index:9;
		border-radius:3px;
		background:#FE123E;
		color:#ffffff;
		text-shadow:0px 1px 0px rgba(0,0,0,0.3);
		padding:11px 15px 11px;
		margin:10px 0px;
		font-size:18px;
		font-weight:normal;
		letter-spacing:-0.5px;
		line-height:25px;
		cursor: pointer;
	}
	#d_windows ul.level1 li:hover {
		background:#F89BAC;
	}

   form#form_calc {margin:0px}

   #d_windows ul.level1 li.on {
		background:#FE123E;
		color:#ffffff;
		text-shadow:0px 1px 0px rgba(0,0,0,0.3);
	}



	#d_windows ul.level2{
		display:inline-block;
		float:left;
		padding:0px;
		width:35%;
		min-height:320px;
		min-width:170px;
		margin:0px;
		background:#f7f7f7;
		overflow:hidden;
		margin-bottom:10px;
		border-radius:3px 0px 0px 3px;
	}


	#d_windows ul.level2 li {
		margin:0px;
		padding:0px;
		background:#f7f7f7;
		list-style:none;
		border-bottom:1px solid #e7e7e7;
		border-right:1px solid #e7e7e7;
		padding:15px;
		margin:0px;
		color:#000000;
		font-size:14px;
		line-height:18px;
		display:block;
		color:#333;
		text-decoration:none;
		cursor:pointer;
	}


	#d_windows ul.level2 li:hover {
		background:#e7e7e7;
	}


	#d_windows ul.level2 li.on {
		border-right:1px solid #ffffff;
	}


	#d_windows ul.level2 li.on{
		background:#ffffff;
		color:#000000;
	}


	#d_windows ul.level2 li.on:hover {
		color:#FE123E;
	}


	#d_windows .tab-block {
		display:block;
		position:relative;
		height:320px;
		overflow:hidden;
		padding:15px;
		background:#FE123E;
	}


	#d_windows .div-tab2 {
		display:none;
		overflow:hidden;
	}


	#d_windows .div-tab2 {
		background:#ffffff;
		height:280px;
		padding:20px 15px;
		overflow:hidden;
		margin-bottom:20px;
		border-radius:0px 3px 3px 0px;
	}


	#d_windows .div-tab2 .window-item {
		text-align:center;
		position:relative;
		border:1px solid #e7e7e7;
		height:260px;
		padding:10px;
		border-radius:2px;
		cursor:pointer;
	}


	#d_windows .div-tab2 .window-item:hover {
		border:1px solid #FE123E;
	}


	#d_windows .div-tab2 .window-item img {
	}

	#calc2-table {
		position:relative;
		display:block;
		width:100%;
		min-height:340px;
		background:#FE123E;
		padding:10px;
		border-radius:5px;
		overflow:hidden;
		box-sizing:border-box;
		-moz-box-sizing:border-box;
		-webkit-box-sizing:border-box;
	}


	#calc2-table .container {
		display:table;
		width:72%;
		text-align:center;
		table-layout:fixed;
		vertical-align:top;
		border-radius:3px 0px 0px 3px;
		overflow:hidden;
	}


	#calc2-table .cell {
		display:table-cell;
		max-width:50%;
		background:#ffffff;
		height:345px;
		vertical-align:top !important;
		border-radius:3px 0px 0px 3px;
	}


	#calc2-table .cell .cell-cnt, #calc2-table .cell-block .cell-cnt {
		display:block;
		padding:0px;
		margin:10px;
		height:300px;
	}


	#calc2-table .cell .img-block, #calc2-table .cell-block .img-block {
		position:relative;
		display:inline-block;
		vertical-align:top;
		top:10px;
	}


	#calc2-table .cell .img-block div, #calc2-table .cell .img-block span {
		position:absolute;
		text-align:center;
		color: #333;
		font-size: 13px;
		letter-spacing: 0px;
	}



	#calc2-table .cell .img-block #slider-height {
		left:-20px;
		top:0px;
	}


	#calc2-table .cell .img-block #slider-width {
		bottom:-20px;
		left:0px
	}


	#calc2-table .cell .img-block span.width1 {
		bottom:-60px;
		left:50%;
		width:200px;
		margin-left:-100px;
		line-height:12px;
	}


	#calc2-table .cell .img-block span.height1 {
		top:50%;
		margin-top:-10px;
		left:-140px;
		line-height:12px;
	}



	#calc2-table .cell .img-block #slider-d_height {
		right:-20px;
		top:0px;
	}


	#calc2-table .cell .img-block #slider-d_width {
		bottom:-20px;
		left:0px;
	}


	#calc2-table .cell .img-block span.width2 {
		bottom:-55px;
		left:50%;
		width:200px;
		margin-left:-100px;
		line-height:12px;
	}


	#calc2-table .cell .img-block span.height2 {
		top:50%;
		margin-top:-10px;
		right:-140px;
		line-height:12px;
	}


	#calc2-table .cell-cnt {
		vertical-align:top;
	}


	#calc2-table .cell .img-block span input {
		background:none;
		display:inline;
		width:40px;
		border:none;
	}


	#calc2-table .add {
		float:left;
		border-radius:0px 3px 3px 0px;
		display:inline-block;
		width:25%;
		padding:15px 15px;
		height:315px;
		margin:0px;
		background:#F7F7F7;
	}


	#calc2-table .add h3 {
		margin:0px;
		margin-bottom:10px;
	}


	#calc2-table .add input[type="button"] {
		width:100%;
		font-size:20px;
		text-transform:uppercase;
		font-family: 'PT Sans Narrow', sans-serif;
		display:block;
		font-weight:normal;
		padding:7px 0px;
		text-align:center;
		margin-bottom:10px;
		-moz-border-radius:3px;
		-webkit-border-radius:3px;
		border-radius:3px;
		color:#ffffff;
		text-decoration:none;
		background-color:#1b87c6;
		border:1px solid #0a77b5;
		text-shadow:0px 1px 0px #0f6ab3;
		box-shadow:inset 1px 0px 0px #209fd1, inset -1px 0px 0px #209fd1, inset 0px 1px 0px #209fd1, inset 0px -1px 0px #209fd1, 0px 1px 0px rgba(0,0,0,0.2);
		cursor:pointer;
	}


	#calc2-table .add input[type="button"]:hover {
		background:#209fd1;
		color:#ffffff;
		border:1px solid #1b87c6;
	}


	#calc2-table .add label {
		line-height:10px;
		margin-bottom:3px;
		vertical-align:top;
		cursor:pointer;
		color:#000000;
	}


	#calc2-table .add label:hover {
		color:#FE123E;
	}


	#d_result {
		display:block;
		background:#F7F7F7;
		color:#000000;
		padding:15px 15px;
		border-radius:5px;
		width:100%;
		box-sizing:border-box;
		-moz-box-sizing:border-box;
		-webkit-box-sizing:border-box;
		margin-top:10px;
		text-align:left;
		position:relative;
	}


	#d_result span {
		background:#ffffff;
		color:#000000;
		border-radius:3px;
		display:inline-block;
		width:auto;
		padding:10px 15px;
		margin:0px 5px 5px 0px;
	}


	#d_result span b {
		color:#FE123E;
	}


	#d_result p {
		background:#ffffff;
		color:#808080;
		border-radius:3px;
		display:inline-block;
		width:50%;
		padding:10px 15px;
		margin:0px;
		line-height:11px;
		font-size:11px;
		text-transform:uppercase;
		overflow:hidden;
	}


	#d_result h2 {
		margin:0px;
		margin-bottom:20px;
		display:inline-block;
		padding:30px;
		background:#FE123E;
		color:#ffffff;
		font-size:55px;
		letter-spacing:0px;
		border-radius:10px;
		box-shadow:inset 0px 5px 5px rgba(0,0,0,0.2);
	}


	#d_result #price {
		text-align:left;
		border:0px;
		width:45%;
		float:left;
		margin-right:20px;
	}


	#d_result #price th {
		text-align:left;
		border:0px;
		padding:8px 10px;
		background:#FE123E;
		color:#ffffff;
		font-weight:bold;
	}


	#d_result #price tr:nth-child(even) {
		background:#ffeb88;
	}


	#d_result #price tr:nth-child(odd) {
		background:#ffffff;
	}


	#d_result #price tr {
		text-align:left;
		border:0px;
		padding:8px 10px;
	}


	#d_result #price td, #d_result #price th {
		text-align:left;
		border:0px;
		padding:8px 10px;
	}


	#d_result .recalc {
		display:inline-block;
		position:absolute;
		background:#ffffff;
		text-transform:uppercase;
		font-weight:bold;
		border-radius:3px;
		padding:10px 15px;
		top:15px;
		right:15px;
		font-size:13px;
	}


	div.info {
		display:none;
	}


</style>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>