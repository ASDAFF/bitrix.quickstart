if(!Array.prototype.indexOf){Array.prototype.indexOf=function(elt){var len = this.length;var from = Number(arguments[1]) || 0;from = (from < 0)? Math.ceil(from): Math.floor(from);if (from < 0)from += len;for (; from < len; from++){if (from in this && this[from] === elt)return from;}return -1;};}

var d = document, rating = {
	id: null,
	element: null,
	callback: function(data){},
	run: function( id, callback )
	{
		var element = document.getElementById( id );
		this.callback = callback;
		
		for( var i=1; i<=5; i++)
		{
			element.innerHTML += '<span class="ratingstar" id="ratingstar' + i + '_' + id + '" onmouseover="rating.over(this)" onmouseout="rating.out(this)" onclick="rating.change(this)"><input type="radio" name="rating" value="'+i+'" /></span>';
		}
	},
	checked: function( element )
	{
		if( element.className.split(' ').indexOf('rating-checked') > -1 )
		{
			return true;
		}
		return false;
	},
	removeClass: function( element, _cls )
	{
		if( element != null && typeof element.className != 'undefined' )
		{
			var c = element.className.split(' '), index = c.indexOf( _cls );
			if( index!=-1 ){
				c.splice( index, 1 );
				element.className = c.join(' ');
			}
		}
	},
	addClass: function( element, _cls )
	{
		if( element != null && typeof element.className != 'undefined' )
		{
			var c = element.className.split(' '), index = c.indexOf( _cls );
			if( index < 0 ) {
				c.push( _cls );
				element.className = c.join(' ');
			}
		}
	},
	change: function( element )
	{
		var tmp = element.id.split('_'),
			val = parseInt( tmp[0].replace('ratingstar','') ),
			id = tmp[1];
		
		if(this.checked( d.getElementById( id ) )) return false;
		
		if([1,2,3,4,5].indexOf( val )>=0)
		{
			d.getElementById( id ).className += ' rating-checked';
			
			for(var i=1; i<=val; i++)
			{
				rating.addClass( d.getElementById('ratingsta' + i + '_' + id), 'ratingstar-active' );
			}
			
			d.getElementById( id ).innerHTML += '<span class="rating-clear" id="ratingclear-'+id+'" onclick="return rating.clear(\''+id+'\');"></span>';
			this.callback( val );
		}
	},
	over: function( element )
	{
		var tmp = element.id.split('_'),
			to = parseInt( tmp[0].replace('ratingstar','') ),
			id = tmp[1];

		if(this.checked( d.getElementById( id ) )) return false;
	
		if([1,2,3,4,5].indexOf( to )>=0)
		{
			for(var i=1; i<=to; i++)
			{
				rating.addClass( d.getElementById('ratingstar' + i + '_' + id), 'ratingstar-active' );
			}
		}
	},
	out: function( element )
	{
		var tmp = element.id.split('_'),
			to = parseInt( tmp[0].replace('ratingstar','') ),
			id = tmp[1];

		if(this.checked( d.getElementById( id ) )) return false;
		
		if([1,2,3,4,5].indexOf(to)>=0)
		{
			for(var i=1; i<=to; i++)
			{
				d.getElementById('ratingstar' + i + '_' + id).className = 'ratingstar';
			}
		}
	},
	clear: function( id )
	{
		if(this.checked( d.getElementById( id ) ))
		{
			var self = this;
			for(var i=1; i<=5; i++)
			{
				d.getElementById('ratingstar' + i + '_' + id).className = 'ratingstar';
			}
			d.getElementById( id ).className = 'rating';
			d.getElementById( id ).removeChild( d.getElementById('ratingclear-'+id) );
		}
	}
};