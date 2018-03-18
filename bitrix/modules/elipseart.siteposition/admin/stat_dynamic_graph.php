<?
//define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/colors.php");

IncludeModuleLangFile(__FILE__);

if(!CModule::IncludeModule("elipseart.siteposition"))
	die();

$module_id = "elipseart.siteposition";

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$width = COption::GetOptionInt($module_id, "GRAPH_WEIGHT");
$height = COption::GetOptionInt($module_id, "GRAPH_HEIGHT");

$graph_type = ($_GET["graph_type"] == "TOP10") ? "TOP10" : "STD";
/*
$date_end = $_GET["end_date"];

$period = intval($_GET["period"]);
if($period > 90)
	$period = 90;

$date_end = explode("-",ConvertDateTime($date_end, "YYYY-MM-DD"));
$date_end = mktime(0,0,0,$date_end[1],$date_end[2],$date_end[0]);
$find_DATE2 = ConvertTimeStamp($date_end+86400, "SHORT");
$find_DATE1 = ConvertTimeStamp($date_end-86400*$period, "SHORT");
*/


$rsData2 = CEASitePosition::GetList(
	array("DATE"=>"DESC"),
	array(
		"KEYWORD_ID" => $arParam["KEYWORD_ID"],//$res["ID"],
		"SEARCH_NAME" => $arParam["SEARCH_NAME"],
		//">DATE" => ConvertDateTime($find_DATE1, "YYYY-MM-DD"),
		//"<DATE" => ConvertDateTime($find_DATE2, "YYYY-MM-DD"),
	),
	false//7
);
while($res2 = $rsData2->Fetch())
{
	$res2["DATE"] = explode(" ",$res2["DATE"]);
	$res2["DATE"] = $res2["DATE"][0];
	$arrPosition[] = array(
		$res2["DATE"],
		$res2["POSITION"],
		$res2["SEARCH_SYSTEM"],
	);
}

$ssDB = CEASitePositionSearchSystem::GetList(array(),array("ACTIVE"=>"Y"));
while($res = $ssDB->Fetch())
{
	$arSearchSystem[] = $res;
}

foreach($arSearchSystem as $val)
{
	$arrParam = array();
	
	foreach($arrPosition as $arParam)
	{
		if($val["NAME"] == $arParam[2])
		{
			$arrParam[] = $arParam;
		}
	}
		
	$arGraphParam[$val["NAME"]] = $arrParam;
}

$arrParam = $arGraphParam;

$arrX=Array();
$arrY=Array();
$arrayX=Array();
$arrayY=Array();

$date = array();

?>
<div id="overview" class="overview" style="width: <?=($width - 25)?>px; height: 70px;"></div>
<div id="placeholder" class="placeholder" style="width: <?=$width?>px; height: <?=$height?>px;"></div>
<div id="graphicsLegend" class="graphicsLegend" style="width: auto; height: auto; min-height: 100px;"></div>
<div class="clear"></div>

<script>
$(function () {
	
	function weekendAreas(axes) {
		var markings = [];
		var d = new Date(axes.xaxis.min);
		
		d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
		d.setUTCSeconds(0);
		d.setUTCMinutes(0);
		d.setUTCHours(0);
		var i = d.getTime();
		do {
			markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 }, color: "rgb(251,251,251)" });
			i += 7 * 24 * 60 * 60 * 1000;
		} while (i < axes.xaxis.max);
		
		return markings;
	}
	
	function showTooltip(x, y, contents) {
		$('<div id="tooltip" class="graphTooltip">' + contents + '</div>').css({
			top: y,
			left: x,
		    opacity: 1.0
		}).appendTo("body").fadeIn(200);
		
		$('#tooltip').css({
			top: y - 30,
			left: x - $('#tooltip').width()/2,
		    opacity: 1.0
		});
	}
	
	<?
	$date = array();
	$graphData = "";
	
	$x = 0;
	foreach($arrParam as $val)
	{
		?>
		var d<?=$x+1?> = {
			data: [<?
				$i = 0;
				foreach($val as $val2)
				{
					if($i > 0)
						echo ", ";
					
					$coordY = $val2[1] > 0 ? 100-$val2[1] : -1;
					
					?>[<?=strtotime($val2[0]." UTC")*1000?>, <?=$coordY?>]<?
					
					$date[] = $val2[0];
					
					++$i;
				}
			?>],
			label: "<?=$arLegend[$x]["NAME"]?>",
			color: "<?=$arLegend[$x]["COLOR"]?>"/*<?=$x+1?>*/
		};
		<?
		
		if($x > 0)
			$graphData .= ",";
		
		$graphData .= "d".($x+1);
		
		++$x;
	}
	
	$graphDataSmall = "";
	
	$x = 0;
	foreach($arrParam as $val)
	{
		?>
		var ds<?=$x+1?> = {
			data: [<?
				$i = 0;
				foreach($val as $val2)
				{
					if($i > 0)
						echo ", ";
					
					$coordY = $val2[1] > 0 ? 100-$val2[1] : -1;
					
					?>[<?=strtotime($val2[0]." UTC")*1000?>, <?=$coordY?>]<?
					
					++$i;
				}
			?>],
			color:"<?=$arLegend[$x]["COLOR"]?>"/*<?=$x+1?>*/
		};
		<?
		
		if($x > 0)
			$graphDataSmall .= ",";
		
		$graphDataSmall .= "ds".($x+1);
		
		++$x;
	}
	
	$date = array_unique($date);
	
	$MinX = min($date);
	$MaxX = max($date);
	?>
	
	var graphData = [<?=$graphData?>];
	var graphDataSmall = [<?=$graphDataSmall?>];
	
	
	var optionsLegend = {
		yaxis: { show: false },
		xaxis: { show: true },
		legend: {
			show: true,
			container: BX("graphicsLegend"),
			noColumns: 2
		}
	};
	var legend = $.plot( $("#graphicsLegend"), graphData, optionsLegend);
	
	$("#graphicsLegend").append('<div id="graphicsLegend2" style="display: none;"></div>');
	
	var choiceContainer = $("#graphicsLegend2");
	$.each(graphData, function(key, val) {
		choiceContainer.append('<br/><input type="checkbox" name="graphicPos_' + key + '" checked="checked" id="graphicPosId_' + key + '">' + '<label for="graphicPosId_' + key + '">' + val.label + '</label>');
	});
	choiceContainer.find("input").click(plotAccordingToChoices);
	
	var i = 0;
	$('#graphicsLegend').find("tr").each(function(){
		
		var y = 0;
		
		$(this).find("td").each(function(){
			
			$(this).css('cursor','pointer');
			
			if(y > 1) { y = 0; ++i; }
			
			$(this).attr("name","legendId_" + i);
			
			$(this).bind("click", function(){
				
				thisId = $(this).attr("name").split("legendId_").join("");
				
				if($('#graphicPosId_' + thisId + '').attr('checked') == true)
				{
					$('#graphicPosId_' + thisId + '').attr('checked',false);
					$('#graphicsLegend').find('td[name="legendId_' + thisId + '"]').each(function(){
						$(this).css('textDecoration','line-through');
					});
				}
				else
				{
					$('#graphicPosId_' + thisId + '').attr('checked','checked');
					$('#graphicsLegend').find('td[name="legendId_' + thisId + '"]').each(function(){
						$(this).css('textDecoration','none');
					});
				}
				
				plotAccordingToChoices();
				
			});
			
			++y;
			
		});
		
		++i;
		
	});
	
	
	var options = {
		yaxis: {
			show: true,
			min: <?=($graph_type == "TOP10") ? 90 : 1?>,
			max: <?=($graph_type == "TOP10") ? 100 : 100?>,
			tickSize: <?=($graph_type == "TOP10") ? 1 : 10?>,
			ticks: [
				<?=($graph_type == "TOP10")
				? "
				//[90, 10],
				[91, 9],
				[92, 8],
				[93, 7],
				[94, 6],
				[95, 5],
				[96, 4],
				[97, 3],
				[98, 2],
				[99, 1]
				"
				: "
				[10, 90],
				[20, 80],
				[30, 70],
				[40, 60],
				[50, 50],
				[60, 40],
				[70, 30],
				[80, 20],
				[90, 10],
				[100, 1]
				"
				?>
			]
		},
		xaxis: {
			show: true,
			mode: "time",
			minTickSize: [1, "day"],
			monthNames: [
				"<?=GetMessage("STAT_GRAPH_MONTH_01")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_02")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_03")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_04")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_05")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_06")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_07")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_08")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_09")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_10")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_11")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_12")?>"
			],
			timeformat: "%d %b"
		},
		selection: { mode: "x" },
		grid: { 
			//markings: weekendAreas,
			markings: [
				{ xaxis: { from: <?=strtotime($MinX." UTC")*1000?>, to: <?=strtotime($MinX." UTC")*1000?> }, color: "rgb(0,0,0)" },
				{ yaxis: { from: 1, to: 1 }, color: "rgb(0,0,0)" }
			],
			labelMargin: 0,
			borderWidth: 0,
			color: "rgb(50,50,50)",
			//backgroundColor: "rgb(245,245,245)",
			hoverable: true,
			clickable: false,
			mouseActiveRadius: 5
		},
		series: {
			lines: { show: true, lineWidth: 2 },
			points: { show: false, radius: 1 },
			shadowSize: 0
		},
		legend: {
			show: false
			/*container: BX("graphicsLegend"),
			noColumns: 2*/
		}
	};
	
	var optionsOverview = {
		series: {
			lines: { show: true, lineWidth: 1 },
			shadowSize: 0
		},
		grid: {
			borderWidth: 1
		},
		//xaxis: { ticks: [], mode: "time" },
		xaxis: {
			show: true,
			mode: "time",
			minTickSize: [1, "month"],
			monthNames: [
				"<?=GetMessage("STAT_GRAPH_MONTH_01")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_02")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_03")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_04")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_05")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_06")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_07")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_08")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_09")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_10")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_11")?>",
				"<?=GetMessage("STAT_GRAPH_MONTH_12")?>"
			],
			timeformat: "%b %y"
		},
		yaxis: { ticks: [], min: 0 },
		selection: { mode: "x" }
	}
	
	function plotAccordingToChoices() {
		
		var data = [];
		var dataSmall = [];
		
		choiceContainer.find("input:checked").each(function () {
			
			var key = $(this).attr("name").split("graphicPos_").join("");
			
			if (key && graphData[key])
				data.push(graphData[key]);
			
			if (key && graphDataSmall[key])
				dataSmall.push(graphDataSmall[key]);
		});
		
		var plot = $.plot( $("#placeholder"), data, options);
		var overview = $.plot($("#overview"), dataSmall, optionsOverview);
		
		$("#placeholder").unbind();
		$("#overview").unbind();
		
		if (data.length > 0)
		{
		    $("#placeholder").bind("plotselected", function (event, ranges) {
				
				plot = $.plot($("#placeholder"), data,
					$.extend(true, {}, options, {
						xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
					}));
		 		
				 overview.setSelection(ranges, true);
			});
			
			$("#overview").bind("plotselected", function (event, ranges) {
				plot.setSelection(ranges);
			});
			
			$("#placeholder").bind("plothover", function (event, pos, item) {
		        //$("#x").text(pos.x.toFixed(2));
		        $("#y").text(pos.y.toFixed(2));
		
		        //if ($("#enableTooltip:checked").length > 0) {
		            if (item) {
		                if (previousPoint != item.dataIndex) {
		                    previousPoint = item.dataIndex;
		                    
		                    $("#tooltip").remove();
		                    var y = item.datapoint[1].toFixed(2);
		                    y = y.split(".00").join("");
		                    y = Number(100) - Number(y);
		                    
		                    showTooltip(item.pageX, item.pageY,
		                                item.series.label + " &mdash; " + y);
		                }
		            }
		            else {
		                $("#tooltip").remove();
		                previousPoint = null;            
		            }
		        //}
		    });
  		}
	
	}
	
	plotAccordingToChoices();

});
</script>