/**
 * Created by anton on 24.01.14.
 */
function countdown_dashboard()
{
    $(".countdown_dashboard").each(function(){
        //get params
        var year = $(this).data('year');
        var month = $(this).data('month')-1;
        var day = $(this).data('day');
        var hours = $(this).data('hours');
        var minutes = $(this).data('minutes');
        var seconds = $(this).data('seconds');
        //get target date
        var targetDate = new Date(
            year,
            month,
            day,
            hours,
            minutes,
            seconds
        );
        //get current date
        var currentDate = new Date();
        //math date
        var diff = targetDate - currentDate;
        var diffDays = $.trim(Math.floor(diff/1000/60/60/24));
        var diffHours = $.trim(Math.floor((diff-diffDays*24*60*60*1000)/1000/60/60));
        var diffMinutes = $.trim(Math.floor((diff-diffDays*24*60*60*1000-diffHours*60*60*1000)/1000/60));
        //prepare math day
        if(diffDays<0 || diffHours<0 || diffMinutes<0) {
            $(".time-buy-catalog").hide();
            $(".card-time-min").hide();
            $(".time-buy").hide();
            return false;
        }
        $(".card-time-min").show();
        if(diffDays.length==0)diffDays = "00";
        if(diffDays.length==1)diffDays = "0"+diffDays;
        //prepare math hours
        if(diffHours.length==0)diffHours = "00";
        if(diffHours.length==1)diffHours = "0"+diffHours;
        //prepare math minuts
        if(diffMinutes.length==0)diffMinutes = "00";
        if(diffMinutes.length==1)diffMinutes = "0"+diffMinutes;
        //set math day
        $(this).find(".days_dash .digit").eq(0).text(parseInt(diffDays.substr(0,diffDays.length-1)));
        $(this).find(".days_dash .digit").eq(1).text(parseInt(diffDays.substr(diffDays.length-1,1)));
        //set math hours
        $(this).find(".hours_dash .digit").eq(0).text(parseInt(diffHours.substr(0,diffHours.length-1)));
        $(this).find(".hours_dash .digit").eq(1).text(parseInt(diffHours.substr(diffHours.length-1,1)));
        //set math minuts
        $(this).find(".minutes_dash .digit").eq(0).text(parseInt(diffMinutes.substr(0,diffMinutes.length-1)));
        $(this).find(".minutes_dash .digit").eq(1).text(parseInt(diffMinutes.substr(diffMinutes.length-1,1)));
    });
}
jQuery(document).ready(function () {
    countdown_dashboard();
    setInterval(countdown_dashboard,1000);
});