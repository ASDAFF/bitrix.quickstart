var buildString = "";

$(document).ready(function() {
    $(".twitter_content").height($(".twitter_content").parent().height() - $(".twitter_content").parent().find(".twitter_head").height() - 8);
   
    if (tweetUsers[0].substr(0,1) == "#") 
	{
		buildString = "&tag=" + tweetUsers[0].substr(1,tweetUsers[0].length - 1);
	}
	else
	{
		for(var i=0;i<tweetUsers.length;i++)
		{
			if(i!=0) buildString+='+OR+';
			buildString+='from:'+tweetUsers[i];
		}
	}
    var fileref = document.createElement('script');
    fileref.setAttribute("type","text/javascript");
    fileref.setAttribute("src", "http://search.twitter.com/search.json?q="+buildString+"&callback=TweetTick&rpp=20");
    document.getElementsByTagName("head")[0].appendChild(fileref);
});

function TweetTick(ob) {

    var container=$('.twitter_content');
    container.removeClass('preloader');
    var i =0;
    $(ob.results).each(function(el) {
        var str = '<div class="twitter_post"><div class="twitter_pic_div"><img src="'+this.profile_image_url+'" alt="'+this.from_user+'" width="38" height="38" class="twitter_pic" /><a href="http://twitter.com/'+this.from_user+'" target="_blank" class="twitter_pic_frame"></a></div><div class="twitter_info"><p class="twitter_login"><a href="http://twitter.com/'+this.from_user+'" target="_blank" title="'+this.from_user+'">'+this.from_user+'</a></p><p class="twitter_time">'+relativeTime(this.created_at)+'</p></div><br class="clear" /><p class="twitter_text">'+formatTwitString(this.text)+'</p></div>';
        container.append(str);

    });

    $('.twitter_content').jScrollPane();
	/*
    var IE='\v'=='v'; if(IE) {
        var width = $('.twitter_content').width() + 4;
        $('.twitter_content').width(width);
    }*/
}

function relativeTime(pastTime) {    
    var origStamp = Date.parse(pastTime);
    var curDate = new Date();
    var currentStamp = curDate.getTime();
    var difference = parseInt((currentStamp - origStamp)/1000);
    var dif60 = makeMinsText(parseInt(difference/60));
    var dif3600 = makeHoursText(Math.round(difference/3600));
    
    if(difference < 0) return false;
    if(difference <= 5) return TWLINE_NOW;
    if(difference <= 20) return TWLINE_SECONDS_AGO;
    if(difference <= 60) return TWLINE_ONE_MINUTE;
    
    if(difference < 3600) return parseInt(difference/60)+" "+ dif60;
    if(difference <= 1.5*3600) return TWLINE_HOUR;
    if(difference < 23.5*3600) return Math.round(difference/3600)+" "+ dif3600;
    if(difference < 1.5*24*3600) return TWLINE_YESTERDAY;
    var dateArr = pastTime.split(' ');
    var formatted_date = dateArr[4].replace(/\:\d+$/,'')+' '+dateArr[2]+' '+dateArr[1]+(dateArr[3]!=curDate.getFullYear()?' '+dateArr[3]:'');
    var formatted_date = decorateDateRussian(formatted_date);
    return (formatted_date);
}

function decorateDateRussian(formatted_date) {
    var dateArr = formatted_date.split(" ");
    var time = dateArr[0];
    var month = dateArr[1];
    var date = dateArr[2];
    switch(month) {
        case "Jan": month = TWLINE_JAN; break
        case "Feb": month = TWLINE_FEB; break
        case "Mar": month = TWLINE_MAR; break
        case "Apr": month = TWLINE_APR; break
        case "May": month = TWLINE_MAY; break
        case "Jun": month = TWLINE_JUN; break
        case "Jul": month = TWLINE_JUL; break
        case "Aug": month = TWLINE_AUG; break
        case "Sep": month = TWLINE_SEP; break
        case "Oct": month = TWLINE_OCT; break
        case "Nov": month = TWLINE_NOV; break
        case "Dec": month = TWLINE_DEC; break
    }
    return(date + " " + month + ", " + time);
}

function makeHoursText(hours) {
    var part_hours = hours%10;
    if (part_hours == 1 && hours != 11) return TWLINE_ONE_HOUR;
    if (part_hours >= 1 && part_hours < 5 && (hours < 10 || hours > 20)) return TWLINE_HOUR;
    if (part_hours == 0 || part_hours > 4 || (hours > 10 && hours < 20)) return TWLINE_HOURS;
}

function makeMinsText(mins) {
    var part_mins = mins%10;
    if (part_mins == 1 && mins != 11) return TWLINE_ONE_MINUTE;
    if (part_mins >= 1 && part_mins < 5 && (mins < 10 || mins > 20)) return TWLINE_MINUTE;
    if (part_mins == 0 || part_mins > 4 || (mins > 10 && mins < 20)) return TWLINE_MINUTES;
}

function formatTwitString(str)
{
    str=' '+str;
    str = str.replace(/((ftp|https?):\/\/([-\w\.]+)+(:\d+)?(\/([\w/_\.]*(\?\S+)?)?)?)/gm,'<a href="$1" target="_blank">$1</a>');
    str = str.replace(/([^\w])\@([\w\-]+)/gm,'$1@<a href="http://twitter.com/$2" target="_blank">$2</a>');
    str = str.replace(/([^\w])\#([\w\-]+)/gm,'$1<a href="http://twitter.com/search?q=%23$2" target="_blank">#$2</a>');
    return str;
}