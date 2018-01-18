<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<!-- /content-header -->
<section class="container" id="content-container">
	<div class="row-fluid">
		<section class="span12 blog posts" id="content">
			<article class="post single">

			
				<div class="post-offset">
					<div class="row-fluid months">
						<div class="span12">
							<a href="#" class="prev">&laquo;</a> <span id="event-header"><?=GetMessage("January")?>
								2013</span> <a href="#" class="next">&raquo;</a>
						</div>
					</div>

					<table id="upcoming-events">
						<thead>
							<tr>
								<th><?=GetMessage("Mon")?></th>
								<th><?=GetMessage("Tue")?></th>
								<th><?=GetMessage("Wed")?></th>
								<th><?=GetMessage("Thu")?></th>
								<th><?=GetMessage("Fri")?></th>
								<th><?=GetMessage("Sat")?></th>
								<th><?=GetMessage("Sun")?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="border-image: none;"><span>1</span></td>
								<td style="border-image: none;"><span>2</span></td>
								<td style="border-image: none;"><span>3</span></td>
								<td style="border-image: none;"><span>4</span></td>
								<td style="border-image: none;"><span>5</span></td>
								<td style="border-image: none;"><span>6</span></td>
								<td style="border-image: none;"><span>7</span></td>
							</tr>

							<tr>
								<td style="border-image: none;"><span>8</span></td>
								<td style="border-image: none;"><span>9</span></td>
								<td style="border-image: none;"><span>10</span></td>
								<td style="border-image: none;"><span>11</span></td>
								<td style="border-image: none;"><span>12</span></td>
								<td style="border-image: none;"><span>13</span></td>
								<td style="border-image: none;"><span>14</span></td>
							</tr>

							<tr>
								<td style="border-image: none;"><span>15</span></td>
								<td style="border-image: none;"><span>16</span></td>
								<td style="border-image: none;"><span>17</span></td>
								<td style="border-image: none;"><span>18</span></td>
								<td style="border-image: none;"><span>19</span></td>
								<td style="border-image: none;"><span>20</span></td>
								<td style="border-image: none;"><span>21</span></td>
							</tr>

							<tr>
								<td style="border-image: none;"><span>22</span></td>
								<td style="border-image: none;"><span>23</span></td>
								<td style="border-image: none;"><span>24</span></td>
								<td style="border-image: none;"><span>25</span></td>
								<td style="border-image: none;"><span>26</span></td>
								<td style="border-image: none;"><span>27</span></td>
								<td style="border-image: none;"><span>28</span></td>
							</tr>

							<tr>
								<td style="border-image: none;"><span>29</span></td>
								<td style="border-image: none;"><span>30</span></td>
								<td style="border-image: none;"><span>31</span></td>
								<td style="border-image: none;"><span class="nextmonth">1</span></td>
								<td style="border-image: none;"><span class="nextmonth">2</span></td>
								<td style="border-image: none;"><span class="nextmonth">3</span></td>
								<td style="border-image: none;"><span class="nextmonth">4</span></td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="post-options">
					<ul class="social">
						<li><a href="#" class="share">Share</a></li>

						<li><a href="#" class="facebook-share">Share on Facebook</a></li>

						<li><a href="#" class="twitter-share">Share on Twitter</a></li>
					</ul>
				</div>
			</article>
			<!-- /post -->
		</section>
		<!-- /content -->
	</div>
</section>
<!-- content-container -->

<script>

var months = ['<?=GetMessage("January")?>','<?=GetMessage("February")?>','<?=GetMessage("March")?>','<?=GetMessage("April")?>','<?=GetMessage("May")?>','<?=GetMessage("June")?>','<?=GetMessage("July")?>','<?=GetMessage("August")?>','<?=GetMessage("September")?>','<?=GetMessage("October")?>','<?=GetMessage("November")?>','<?=GetMessage("December")?>'];

var prevMonth = 11;
var prevYear = 2012;

var currentMonth = 2;
var currentYear = 2013;
var table = $('#upcoming-events');
var tableDays = table.find('span');

var inAction = false;

$(document).ready(function(){
	reloadCalendar()
})

$('.next').on('click', function(e){
	e.preventDefault();
	if(inAction) return;
	prevMonth = currentMonth;
	currentMonth++;
	
	if(currentMonth > 11) {
		currentMonth = 1;
		prevYear = currentYear;
		currentYear++;
	} else {
		prevYear = currentYear;
	}
	
	switchItem($('#event-header'), -1);
});

$('.prev').on('click', function(e){
	e.preventDefault();
	if(inAction) return;
	prevMonth = currentMonth;
	currentMonth--;
	if(currentMonth < 0) { 
		currentMonth = 11;
		prevYear = currentYear;
		currentYear--;
	} else {
		prevYear = currentYear;
	}
	switchItem($('#event-header'), 1);
	
});

function switchItem(item, dir){
	inAction = true;
	item.animate({ left : '+='+ 50*dir, opacity : 0}, function(){
		item.animate( { left : '+='+ 100*(dir*-1) }, 0, function(){
			item.html(months[currentMonth] + ' ' + currentYear);
			item.animate( { left : '+='+ 50*dir, opacity : 1}, function(){
				inAction = false;
			})
		})
	});
	reloadCalendar()
}



function reloadCalendar(){

	var d = new Date(currentYear,currentMonth, 1 );
	var firstDay = d.getDay();
	firstDay = firstDay == 0 ? 6 : (firstDay-1)
	var maxDay = DaysInMonth(currentMonth, currentYear);
	var prevMax = DaysInMonth(prevMonth, prevYear);
	var className = '';
	var events = GetEvents(currentYear, currentMonth)
	var day = 1;
		$.each(table.find('tr'), function(i,v){
			$.each($(v).find('td span'), function(j,k){	
				$(k).animate({opacity: 0}, function(){
				$(k).attr('class', className);
				
				if(i == 1){
					if(j >= firstDay){
						$(k).html(day);
						if(day < maxDay) {
							day++;
						}
					} else {
						$(k).html(prevMax-(firstDay-j-1));
						$(k).addClass('nextmonth');
					}
				} else {
					$(k).html(day);
					if(day < maxDay){
						day++;
					} else { 
						day = 1;
						className = 'nextmonth';
					};	
				}
					$(k).animate({opacity: 1})
					
					$(k).removeClass('party');
					$(k).parent().removeClass('party-day');

					if(events[day] != undefined && events[day] != ''){

						$(k).html($(k).html()+events[day])
						$(k).addClass('party');
						$(k).parent().addClass('party-day');
					}
				})
				
			});

		});

}

$('.event-info').live('mouseover', function(){
	
	$(this).stop().animate({opacity : 1, height: 316, top : -10})
	
})

$('.event-info').live('mouseout', function(){
	
	$(this).stop().animate({opacity : 0, height: 100, top : - 30})
	
})

function GetEvents(year,month){

	/* this should be returned from api, number of day and html */
	return {


		<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
			
$file = CFile::ResizeImageGet ($arItem['PREVIEW_PICTURE'], array ('width' => 270, 'height' => 380 ), BX_RESIZE_IMAGE_EXACT, true );
			$day = $arItem[PROPERTIES][DAY][VALUE]+1;
			?>
			<?=$day?> : '<div class="event-info"><ul class="what-when"><li><strong><?=$arItem[NAME]?></strong></li><li><a href="<?=$arItem[DETAIL_PAGE_URL]?>" ><img  src="<?=$file[src]?>"  /></a></li></ul><a href="<?=$arItem[DETAIL_PAGE_URL]?>" class="btn btn-small" ><?=GetMessage("MORE")?></a></div>',
			
		<?php // echo "<pre>"; print_r($arItem); echo "</pre>";?>
		<?endforeach;?>
		
		
		
		 };
}


function DaysInMonth(month, year){return 32 - new Date(year, month, 32).getDate()}
</script>





