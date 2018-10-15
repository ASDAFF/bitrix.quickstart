/*
######################################################
# Name: energosoft.twitter                           #
# File: jquery.energosoft.twitter.js                 #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
*/

(function(jQuery){
	jQuery.extend({
		esTwitter: function(id, name, count, template)
		{
			var url="http://api.twitter.com/1/statuses/user_timeline.json?screen_name="+name+"&count="+count+"&callback=?";
			jQuery.getJSON(url, function(data)
			{
				jQuery("#esTwitter"+id).empty();
				jQuery.each(data, function(i, post)
				{
					var str = template;
					var created_at = jQuery.esTwitterDateParse(post.created_at);
					str = str.replace("%img%", post.user.profile_image_url);
					str = str.replace("%text%", jQuery.esTwitterUrlParse(post.text));
					if(jQuery.isFunction(jQuery.fn.timeago)) str = str.replace("%timeago%", jQuery.timeago(created_at));
					else str = str.replace("%timeago%", created_at.toLocaleDateString());
					str = str.replace("%localedate%", created_at.toLocaleDateString());
					str = str.replace("%localetime%", created_at.toLocaleTimeString());
					jQuery("#esTwitter"+id).append(str);
				});
				jQuery("#esContainer"+id).removeClass("es-twitter-preloader");
				if(jQuery.isFunction(jQuery.fn.jScrollPane)) jQuery("#esContainer"+id).jScrollPane();
			});
		}
	});
})(jQuery);

(function(jQuery){
	jQuery.extend({
		esTwitterUrlParse: function(inputText)
		{
			var replaceText;
			//URLs starting with http://, https://, or ftp://
			replacedText = ' ' + inputText.replace(/(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim, '<a href="$1" target="_blank">$1</a>');
			//URLs starting with "www." (without // before it, or it'd re-link the ones done above).
			replacedText = replacedText.replace(/(^|[^\/])(www\.[\S]+(\b|$))/gim, '$1<a href="http://$2" target="_blank">$2</a>');
			//Change email addresses to mailto:: links.
			replacedText = replacedText.replace(/(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim, '<a href="mailto:$1">$1</a>');
			//Replace the mentions
			replacedText = replacedText.replace(/([^\w])\@([\w\-]+)/gim, '$1@<a href="http://twitter.com/$2" target="_blank">$2</a>');
			//Replace the hashtags
			replacedText = replacedText.replace(/([^\w])\#([\w\-]+)/gim, '$1<a href="http://twitter.com/search?q=%23$2" target="_blank">#$2</a>');
			return replacedText;
		}
	});
})(jQuery);

(function(jQuery){
	jQuery.extend({
		esTwitterDateParse: function(strDate)
		{
			if(jQuery.browser.msie)
			{
				var arDate = strDate.split(" ");
				return new Date(arDate[0]+", "+arDate[2]+" "+arDate[1]+" "+arDate[5]+" "+arDate[3]+" "+arDate[4]);
			}
			else return new Date(strDate);
		}
	});
})(jQuery);