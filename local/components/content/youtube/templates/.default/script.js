var player = [], playing = []; jopa = 0;

	function browser()
	{
		var ua = navigator.userAgent;

		if (ua.search(/MSIE/) > 0) return 'Internet Explorer';
		if (ua.search(/Firefox/) > 0) return 'Firefox';
		if (ua.search(/Opera/) > 0) return 'Opera';
		if (ua.search(/Chrome/) > 0) return 'Google Chrome';
		if (ua.search(/Safari/) > 0) return 'Safari';
		if (ua.search(/Konqueror/) > 0) return 'Konqueror';
		if (ua.search(/Iceweasel/) > 0) return 'Debian Iceweasel';
		if (ua.search(/SeaMonkey/) > 0) return 'SeaMonkey';
		if (ua.search(/Gecko/) > 0) return 'Safari';
		if (ua.search(/Apple/) > 0) return 'Safari';

		return 'Search Bot';
	}

	//document.getElementsByClassName( 'viewsInfo' )[0].innerHTML =

	function init()
	{
		console.log( 'Init Player' );

		// выход из функции, если она уже вызывалась
		if (arguments.callee.done) return;

		// флаг, чтобы не запускать функцию дважды
		arguments.callee.done = true;

		// Load API
		var tag = document.createElement('script');

		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		//
		var elem = document.getElementsByClassName( 'l1-youtube-player' );
		for ( var i = 0; i < elem.length; i++ )
		{
			console.log( 'Resize width && height player #' + i );
			//elem[ i ].addEventListener( 'click', playz( i, elem[ i ] ), false );
			elem[ i ].style.width = ( elem[ i ].getAttribute( 'data-width' ).indexOf( '%' ) != -1 ? elem[ i ].getAttribute( 'data-width' ) : elem[ i ].getAttribute( 'data-width' ) + 'px' );
			elem[ i ].style.height = ( elem[ i ].getAttribute( 'data-height' ).indexOf( '%' ) != -1 ? elem[ i ].getAttribute( 'data-height' ) : elem[ i ].getAttribute( 'data-height' ) + 'px' );
		}

		// Костыль
		if ( jopa == 0 ) onYouTubePlayerAPIReady();
	}


	// Mozilla
	if (document.addEventListener)
	{
		document.addEventListener("DOMContentLoaded", init, false);
	}


	// Safari
	if ( /WebKit/i.test( navigator.userAgent ) )
	{
		var _timer = setInterval(function()
		{
			if (/loaded|complete/.test(document.readyState))
			{
				clearInterval(_timer);
				delete _timer;
				init();
			}
		}, 10);
	}

	/* for Internet Explorer */
	/*@cc_on @*/
	/*@if (@_win32)
		document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
	    var script = document.getElementById("__ie_onload");
		    script.onreadystatechange = function()
		    {
		        if (this.readyState == "complete") {
		        init(); // call the onload handler
		    }
	    };
	 /*@end @*/


	window.onload = init;


	// YouTube Ready
	function onYouTubePlayerAPIReady()
	{
		jopa = 1;
		var elem = document.getElementsByClassName( 'l1-youtube-player' ), dname, dscreen;

		for ( var i = 0; i < elem.length; i++ )
		{
			dname = elem[ i ].getAttribute( 'data-name' );

			if ( dname && dname.length > 0 )
			{
				dscreen = elem[ i ].getElementsByClassName( 'l1-youtube-screen' )[ 0 ];
				dscreen.setAttribute( 'id', dname + i );

				player[ i ] = new YT.Player( dscreen.id,
					{
						height: elem[ i ].getAttribute( 'data-height' ), width: elem[ i ].getAttribute( 'data-width' ),
						videoId: dname,
						playerVars:
						{
							'autoplay': elem[ i ].getAttribute( 'data-autoplay' ),
							'controls': elem[ i ].getAttribute( 'data-controls' ),
							'loop': elem[ i ].getAttribute( 'data-loop' ),
							'iv_load_policy': elem[ i ].getAttribute( 'data-iv_load_policy' ),
							'cc_load_policy': elem[ i ].getAttribute( 'data-cc_load_policy' ),
							'modestbranding': elem[ i ].getAttribute( 'data-logo' ),
							'fs': elem[ i ].getAttribute( 'data-fs' ),
							'origin': elem[ i ].getAttribute( 'data-origin' ),
						},
						events:
						{
							'onStateChange': onPlayerStateChange,
						}
					}
				);

				console.log( 'Player load #' + i );

				elem[ i ].setAttribute( 'onClick', 'playz(' + i + ', this);' );

				if ( browser() == 'Safari' )
				{
					elem[ i ].getElementsByClassName( 'l1-youtube-back' )[ 0 ].parentNode.removeChild( elem[ i ].getElementsByClassName( 'l1-youtube-back' )[ 0 ] );
					elem[ i ].getElementsByClassName( 'l1-youtube-play' )[ 0 ].parentNode.removeChild( elem[ i ].getElementsByClassName( 'l1-youtube-play' )[ 0 ] );
					elem[ i ].getElementsByClassName( 'l1-youtube-preload' )[ 0 ].parentNode.removeChild( elem[ i ].getElementsByClassName( 'l1-youtube-preload' )[ 0 ] );
					elem[ i ].getElementsByClassName( 'l1-youtube-background' )[ 0 ].parentNode.removeChild( elem[ i ].getElementsByClassName( 'l1-youtube-background' )[ 0 ] );
				} else
				{
					if ( elem[ i ].getAttribute( 'data-img' ) != '' )
					{
						elem[ i ].getElementsByClassName( 'l1-youtube-background' )[ 0 ].style.cssText = 'background: url("' + elem[ i ].getAttribute( 'data-img' ) + '"); background-size: cover;';
						elem[ i ].getElementsByClassName( 'l1-youtube-background' )[ 0 ].style.height = elem[ i ].getAttribute( 'data-height' );
						elem[ i ].getElementsByClassName( 'l1-youtube-background' )[ 0 ].style.width = elem[ i ].getAttribute( 'data-width' );

						elem[ i ].getElementsByClassName( 'l1-youtube-play' )[ 0 ].style.display = 'block';
						elem[ i ].getElementsByClassName( 'l1-youtube-background' )[ 0 ].style.display = 'block';
						elem[ i ].getElementsByClassName( 'l1-youtube-back' )[ 0 ].style.display = 'none';
					}

					elem[ i ].getElementsByClassName( 'l1-youtube-preload' )[ 0 ].style.display = 'none';
					elem[ i ].getElementsByClassName( 'l1-youtube-preload' )[ 0 ].innerHTML = 'Loading video...';

				}

				playing[ i ] = false;
			}
		}
	}

	// Change status player
	function onPlayerStateChange( event )
	{
		console.log( 'Change player ' + event.target[ 'a' ].id + ' event: ' + event.data );

		if ( event.data == 1 || event.data == 2 || event.data == 3 )
		{
			var names = event.target[ 'a' ].id;
			var elem = document.getElementsByClassName( 'l1-youtube-player' ), dname = '';

			for ( var i = 0; i < elem.length; i++ )
			{
				dname = elem[ i ].getAttribute( 'data-name' );

				if ( dname && dname.length > 0 )
					if ( dname + i == names )
						playing[ i ] = true;
					else
					{
						if ( playing[ i ] == true )
						{
							player[ i ].stopVideo();

							if ( browser() != 'Safari' && elem[ i ].getAttribute( 'data-img' ) != '' )
							{
								elem[ i ].getElementsByClassName( 'l1-youtube-back' )[ 0 ].style.cssText = 'background: url("' + elem[ i ].getAttribute( 'data-img' ) + '"); background-size: cover;';
								elem[ i ].getElementsByClassName( 'l1-youtube-back' )[ 0 ].style.height = elem[ i ].getAttribute( 'data-height' );
								elem[ i ].getElementsByClassName( 'l1-youtube-back' )[ 0 ].style.width = elem[ i ].getAttribute( 'data-width' );

								elem[ i ].getElementsByClassName( 'l1-youtube-play' )[ 0 ].style.display = 'block';
								elem[ i ].getElementsByClassName( 'l1-youtube-back' )[ 0 ].style.display = 'block';
							}
						}
						playing[ i ] = false;
					}

				console.log( playing[ i ] );
			}
		}

		if ( event.data == 1 )
		{
			var names = event.target[ 'a' ].id;
			var elem = document.getElementsByClassName( 'l1-youtube-player' ), dname = '';

			if ( browser() != 'Safari' )
				for ( var i = 0; i < elem.length; i++ )
				{
					dname = elem[ i ].getAttribute( 'data-name' );

					if ( dname + i == names )
					{
						if ( elem[ i ].getElementsByClassName( 'l1-youtube-back' ) ) elem[ i ].getElementsByClassName( 'l1-youtube-back' )[ 0 ].style.display = 'none';
						if ( elem[ i ].getElementsByClassName( 'l1-youtube-play' ) ) elem[ i ].getElementsByClassName( 'l1-youtube-play' )[ 0 ].style.display = 'none';
						if ( elem[ i ].getElementsByClassName( 'l1-youtube-preload' ) ) elem[ i ].getElementsByClassName( 'l1-youtube-preload' )[ 0 ].style.display = 'none';
					}
				}

		}
	}

	// Play
	function playz( id, obj )
	{
		if ( player[ id ] )
		{
			console.log( 'Click player ' + id );

			if ( browser() != 'Safari' )
			{


				console.log( 'BROWSER DIV DISPLAY NONE' );
				if ( obj.getElementsByClassName( 'l1-youtube-back' ) ) obj.getElementsByClassName( 'l1-youtube-back' )[ 0 ].style.display = 'block';
				if ( obj.getElementsByClassName( 'l1-youtube-preload' ) ) obj.getElementsByClassName( 'l1-youtube-preload' )[ 0 ].style.display = 'block';

				if ( obj.getElementsByClassName( 'l1-youtube-play' ) ) obj.getElementsByClassName( 'l1-youtube-play' )[ 0 ].style.display = 'none';
				if ( obj.getElementsByClassName( 'l1-youtube-background' ) ) obj.getElementsByClassName( 'l1-youtube-background' )[ 0 ].style.display = 'none';
			}

			player[ id ].playVideo();
		}
	}