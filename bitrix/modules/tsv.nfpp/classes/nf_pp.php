<?
/**
 * nf_pp
 *
 * The class is designed to emulate function "print_r" with some additional
 *   features, such as:
 *     - highlight data types;
 *     - highlight properties scope;
 *     - visualize values of the boolean and NULL variables;
 *     - show resource type;
 *     - trim long strings;
 *     - fold nodes in arrays and objects;
 *     - fold whole tree or unfold tree to a certain key;
 *     - print elapsed time between function calls;
 *     - search in keys and values.
 *
 * @author MAYDOKIN Aleksey
 * @version 2.1.0
 */
class nf_pp {

	public
		$trimString    = 100,
		$autoCollapsed = FALSE,
		$autoOpen      = array();

	protected
		$arRecursion = array();

	protected static
		$jsFuncDisp   = FALSE,
		$cssDisp      = FALSE,
		$callCntr     = 0,
		$lastCallTime = 0;

	const TRACE_DEPTH = 0;  //  how many wrap functions has pp-method ( except pp-function )


	function __construct(){

		$options = func_get_args();
		$options = call_user_func_array( array( __CLASS__, 'parseOptions' ), $options );

		if( isset( $options['trimString'] ) )
			$this->trimString = intval( $options['trimString'] );

		if( isset( $options['autoCollapsed'] ) )
			$this->autoCollapsed = $options['autoCollapsed'];

		if( isset( $options['autoOpen'] ) ){

			$options['autoOpen'] = (array)$options['autoOpen'];

			$this->autoOpen      = $options['autoOpen'];
			$this->autoCollapsed = TRUE;

		}

	}


	/**
	 *  Guesses the options
	 */
	function parseOptions(){

		$options = func_get_args();

		if( sizeof( $options ) == 0 )  //  default
			return $options;

		if( is_array( $options[0] ) )  //  trivial options
			return $options[0];

		$newOptions = array();

		foreach( $options as $opt ){

			switch( gettype( $opt ) ){

				case 'boolean':
					$newOptions['autoCollapsed'] = $opt;
					break;

				case 'integer':
					$newOptions['trimString'] = $opt;
					break;

				case 'string':
				case 'array':
					$newOptions['autoOpen'] = $opt;
					break;

			}

		}

		return $newOptions;

	}

	function pp( $val, $curLevel = 0, $key = NULL, $isLast = true ){

		if( $curLevel == 0 ){
			$this->arRecursion = array();  //  drop recursion cache between top-level funciton calls
			$domId = 'pp_' . ++self::$callCntr;
			echo '<div class="pp_wrap" id="'.$domId.'">';
			$this->backtrace();
			$this->timestamp();
			echo '<div><input type="search" class="pp_search"><span class="pp_found"></span></div>';
			echo '<div><a href="javascript:;" class="pp_top">on top</a></div>';
			echo '<ul class="pp_container">';
		}

		//  determine type of the variable
		$valType = gettype( $val );

		//  classes for the node
		$arClasses = array( 'pp_node' );

		if( $curLevel == 0 )
			$arClasses[] = 'pp_isRoot';

		if( $isLast )
			$arClasses[] = 'pp_isLast';

		if( $valType == 'array' || $valType == 'object' )
			$arClasses[] = 'pp_expandOpen';
		else
			$arClasses[] = 'pp_expandLeaf';

		echo '<li class="'.implode( ' ', $arClasses ).'">';
		echo '<div class="pp_expand"></div>';
		echo '<div class="pp_content">';

		if( $key !== NULL )
			echo '<span class="pp_key">['.$key.']</span> => ';


		switch( $valType ){

			case 'boolean':
				$this->p_bool( $val );
				break;

			case 'NULL':
				$this->p_null( $val );
				break;

			case 'integer':
			case 'double':
			case 'float':
				$this->p_basic( $val );
				break;

			case 'string':
				$this->p_string( $val );
				break;

			case 'array':
				$this->p_array( $val, $curLevel );
				break;

			case 'object':
				$this->p_object( $val, $curLevel );
				break;

			case 'resource':
				$this->p_res( $val );
				break;

			default:
				$this->p_unknown( $val );

		}

		echo '</li>';

		if( $curLevel == 0 ){
			echo '</ul>';
			$this->p_css();
			$this->p_jsfunc();
			$this->p_jsinit( $domId );
			echo '</div>';
		}

	}


	protected function p_bool( $val ){

		echo '<span class="pp_bool pp_value">'.strtoupper( var_export( $val, TRUE ) ).'</span></div>';

	}

	protected function p_null( $val ){

		echo '<span class="pp_null pp_value">'.strtoupper( var_export( $val, TRUE ) ).'</span></div>';

	}

	protected function p_basic( $val ){

		echo '<span class="pp_num pp_value">'.$val.'</span></div>';

	}

	protected function p_string( $val ){

		$val = htmlspecialchars( $val );
		if( $this->trimString > 0 && strlen( $val ) > $this->trimString ){

			if( $this->trimString > 3 )
				$val = substr( $val, 0, $this->trimString - 3 ).'...';
			else
				$val = substr( $val, 0, $this->trimString );

		}

		echo '<span class="pp_string pp_value">'.$val.'</span></div>';

	}

	protected function p_array( $val, $curLevel ){

		$size = sizeof( $val );

		echo '<span class="pp_array pp_value">Array</span><i class="pp_ctrl pp_ctrlCollapseCh" title="Fold/unfold children">('.$size.')</i></div>';
		echo '<ul class="pp_container">';

		if( $size ){

			$c = 1;
			foreach( $val as $k => $v )
				echo $this->pp( $v, $curLevel + 1, htmlspecialchars( $k ), $c++ == $size );

		}
		else{

			echo '<li class="pp_node pp_expandLeaf pp_isLast"><div class="pp_expand"></div><div class="pp_content"><span class="pp_empty">EMPTY</span></div></li>';

		}
		echo '</ul>';

	}

	protected function p_object( $val, $curLevel ){

		$className = get_class( $val );
		$val = (array)$val;
		$size = sizeof( $val );

		echo '<span class="pp_object pp_value">Object &lt;'.$className.'&gt;</span><i class="pp_ctrl pp_ctrlCollapseCh" title="Fold/unfold children">('.$size.')</i></div>';
		echo '<ul class="pp_container">';

		if( ! in_array( $val, $this->arRecursion, true ) ){  //  check for recursion

			if( $size ){

				$this->arRecursion[] = $val;

				$c = 1;
				foreach( $val as $k => $v ){

					if( strpos( $k, chr(0).$className.chr(0) ) === 0 ){
						$k = str_replace( chr(0).$className.chr(0), '', $k );
						$k = htmlspecialchars( $k ).':<span class="pp_scope pp_scope_private">private</span>';
					}
					elseif( strpos( $k, chr(0).'*'.chr(0) ) === 0 ){
						$k = str_replace( chr(0).'*'.chr(0), '', $k );
						$k = htmlspecialchars( $k ).':<span class="pp_scope pp_scope_protected">protected</span>';
					}
					else{
						$k = htmlspecialchars( $k ).':<span class="pp_scope pp_scope_public">public</span>';
					}

					echo $this->pp( $v, $curLevel + 1, $k, $c++ == $size );

				}

			}
			else{

				echo '<li class="pp_node pp_expandLeaf pp_isLast"><div class="pp_expand"></div><div class="pp_content"><span class="pp_empty">EMPTY</span></div></li>';

			}

		}
		else {

			echo '<li class="pp_node pp_expandLeaf pp_isLast"><div class="pp_expand"></div><div class="pp_content"><span class="pp_empty">RECURSION</span></div></li>';

		}

		echo '</ul>';

	}

	protected function p_res( $val ){

		echo '<span class="pp_resource pp_value">'.$val.' &lt;'.get_resource_type( $val ).'&gt;</span></div>';

	}

	protected function p_unknown( $val ){

		echo '<span class="pp_unknown pp_value">"'.$val.'"</span></div>';

	}

	/**
	 *  Prints a mark before the tree
	 */
	protected function backtrace(){

		$backtrace = debug_backtrace();

		if( $backtrace[2]['function'] == 'pp' )
			$arToPrint = $backtrace[2 + self::TRACE_DEPTH];  //  run as a function
		else
			$arToPrint = $backtrace[1 + self::TRACE_DEPTH];  //  run as a method

		echo '<div class="pp_mark">'.$arToPrint['file'].' <span title="Line number">'.$arToPrint['line'].'</span></div>';

	}


	/**
	 *  Prints elapsed time between function calls.
	 */
	protected function timestamp(){

		$curTime = microtime( TRUE );

		echo '<div class="pp_mark" title="Elapsed time between function calls">';

		if( self::$lastCallTime > 0 )
			echo ( $curTime - self::$lastCallTime ).' sec.';
		else
			echo 'first call';

		echo '</div>';

		self::$lastCallTime = $curTime;

	}

	protected function p_jsfunc(){

		if( self::$jsFuncDisp )
			return;
		else
			self::$jsFuncDisp = TRUE;

		echo '<script type="text/javascript">
//<![CDATA[
function nf_pp_init( id, autoCollapsed, autoOpen ){

	var
		classCtrl       = \'pp_expand\',
		classOpened     = \'pp_expandOpen\',
		classClosed     = \'pp_expandClosed\',
		classCollapseCh = \'pp_ctrlCollapseCh\',
		classKey        = \'pp_key\',
		classValue      = \'pp_value\',
		classNode       = \'pp_node\',
		classRoot       = \'pp_isRoot\',
		classFoundInText = \'pp_found_in_text\',
		re              =  new RegExp( \'(^|\\\\s)(\'+classOpened+\'|\'+classClosed+\')(\\\\s|$)\' );


	var
		wrap = document.getElementById( id ),
		searchInput = wrap.children[2].firstChild;

	if( autoCollapsed )
		autoCollapseTree( wrap );

	if( autoOpen.length )
		autoOpenTree( wrap, autoOpen );

	applyHdlr( wrap, tree_toggle );
	applyHdlr( searchInput, searchHdlr, \'input\' );
	applyHdlr( searchInput, function(){ if (event.propertyName == \'value\') searchHdlr.apply( this ); }, \'propertychange\' );
	applyHdlr( searchInput, searchKeyHdlr, \'keyup\' );


	function tree_toggle( event ){

		event = event || window.event;
		var clickedElem = event.target || event.srcElement;

		if( hasClass( clickedElem, classCtrl ) ){

			var node = clickedElem.parentNode;

			if( hasClass( node, classOpened ) )
				closeNode( node );
			else if( hasClass( node, classClosed ) )
				openNode( node );

		}
		else if( hasClass( clickedElem, \'pp_ctrlCollapseCh\' ) ) {

			var node = clickedElem.parentNode.parentNode;
			collapseChildren(node);

		}
		else if( hasClass( clickedElem, \'pp_top\' ) ) {

			onTop( wrap );

		}

	}


	function closeNode( node ){

		node.className = node.className.replace( re, \'$1\'+classClosed+\'$3\' );

	}


	function openNode( node ){

		node.className = node.className.replace( re, \'$1\'+classOpened+\'$3\' );

	}


	function openNodeUpWard( node ){

		openNode( node );          //  open current node

		if( ! hasClass( node, classRoot ) ){
			var parent = node.parentNode.parentNode;
			openNodeUpWard( parent );  //  open parent node
		}

	}


	function autoOpenTree( node, autoOpen ){

		var
			arSpan = node.getElementsByTagName( \'SPAN\' ),
			arToOpen = [];

		for( var c = arSpan.length - 1; c >= 0; c-- ){

			var curSpan = arSpan[c];

			if( ! hasClass( curSpan, classKey ) )
				continue;

			for( var q = autoOpen.length - 1; q >= 0; q-- ){

				var rx = new RegExp( \'\\\\[\'+autoOpen[q]+\'(:<span[^<]*</span>)?\\\\]\', \'i\' );

				if( curSpan.innerHTML.search( rx ) != -1 ){
					arToOpen.push( curSpan.parentNode.parentNode );
					break;
				}

			}

		}

		for( var c = arToOpen.length - 1; c >= 0; c-- )
			openNodeUpWard( arToOpen[c] );

	}


	function autoCollapseTree( node ){

		var arLi = node.getElementsByTagName( \'LI\' );

		for( var c = arLi.length - 1; c >= 0; c-- ){

			var curli = arLi[c];
			if( hasClass( curli, classNode ) )
				closeNode( curli );

		}

	}


	function collapseChildren( node ){

		var collapse = false;

		//  get children UL
		var ul = null;
		for( var c = node.children.length - 1; c >= 0; c-- ){

			if( node.children[c].nodeName == \'UL\' ){
				ul = node.children[c];
				break;
			}

		}

		//  get children LIs
		var arLi = null;
		if( ul )
			arLi = ul.children;

		//  determine if there is opened nodes
		for( var c = arLi.length - 1; c >= 0; c-- ){

			if( hasClass( arLi[c], classOpened ) ){
				collapse = true;
				break;
			}

		}

		if( collapse )  //  collapse
			for( var c = arLi.length - 1; c >= 0; c-- )
				closeNode( arLi[c] );
		else            //  open
			for( var c = arLi.length - 1; c >= 0; c-- )
				openNode( arLi[c] );

	}


	function onTop( wrap ){

		if( hasClass( wrap, \'pp_fixed\' ) ){
			wrap.className = wrap.className.replace( \' pp_fixed\', \'\' );
		}
		else {

			var divs = document.getElementsByTagName( \'DIV\' );
			for( var c = divs.length - 1; c >= 0; c-- ){
				if( hasClass( divs[c], \'pp_fixed\' ) ){
					divs[c].className = divs[c].className.replace( \' pp_fixed\', \'\' );
				}
			}

			wrap.className += \' pp_fixed\';

		}

	}


	function searchHdlr(){

		var
			searchString = this.value,
			found = recSearch( this.parentNode.nextSibling.nextSibling, this.value.toLowerCase() );

		this.nextSibling.innerHTML = \'found: \' + found.length;
		this.found = found;

	}


	function searchKeyHdlr( event ){

		event = event || window.event;

		//  hit ENTER
		if( event.keyCode == 13 && this.found && this.found.length ){

			var firstFound = this.found.pop();

			openNodeUpWard( firstFound );
			firstFound.scrollIntoView();

			this.found.unshift( firstFound );

		}

	}


	function recSearch( node, text ){

		var found = [];

		if ( node.tagName == \'SPAN\' && hasClass( node, classKey+\'|\'+classValue ) ) {

			//  remove old marks
			if( node.getElementsByTagName( \'EM\' ).length ){

				var prevNode = null;
				var i = 0;

				while( i < node.childNodes.length ){

					var curNode = node.childNodes[i];

					if( curNode.nodeType == 3 ){

						if( prevNode ){
							prevNode.nodeValue += curNode.nodeValue;
							node.removeChild( curNode );
						}
						else{
							prevNode = curNode;
							++i;
						}

					}
					else if(curNode.nodeType == 1 ){

						if( hasClass( curNode, classFoundInText ) ){
							if( ! prevNode ){
								prevNode = document.createTextNode( \'\' );
								node.insertBefore( prevNode, curNode );
								++i;
							}
							prevNode.nodeValue += curNode.innerText || curNode.textContent;
							node.removeChild( curNode );
						}
						else{
							prevNode = null;
							++i;
						}

					}

				}

			}


			if( text ){

				var
					textLength = text.length,
					textNode = node.childNodes[0],
					startPos = textNode.nodeValue.toLowerCase().indexOf( text );

				if( startPos > -1 ){

					var
						foundText = textNode.nodeValue.substring(
							startPos,
							startPos + textLength
						),
						mark = document.createElement( \'EM\' );

					mark.className = classFoundInText;
					mark.appendChild( document.createTextNode( foundText ) );

					var tail = textNode.splitText( startPos );
					tail.nodeValue = tail.nodeValue.substring( textLength );
					node.insertBefore( mark, tail );

					found.push( node );

				}

			}

		}
		else if( node.children.length ){

			var children = node.children;
			for( var i = children.length - 1; i >= 0; i-- ){
				found = found.concat( recSearch( children[i], text ) );
			}

		}

		return found;

	}


	function hasClass( elem, className ){

		return new RegExp( \'(^|\\\\s)\'+className+\'(\\\\s|$)\' ).test( elem.className );

	}


	/**
	 *  Applies handler to control
	 */
	function applyHdlr( target, handler, type ){

		type = type || \'click\';

		if( target.addEventListener )
			target.addEventListener( type, handler, false );
		else
			target.attachEvent( \'on\' + type, function(){ handler.call( target ) } );

	}

}
//]]>
</script>';

	}


	protected function p_jsinit( $id ){

		echo '<script type="text/javascript">
			nf_pp_init( "'.$id.'", '.( $this->autoCollapsed ? 'true': 'false' ).', '.json_encode( $this->autoOpen ).' );
		</script>';

	}


	protected function p_css(){

		if( self::$cssDisp )
			return;
		else
			self::$cssDisp = TRUE;

		echo '<style type="text/css">
.pp_container {
	padding: 0;
	margin: 0;
}
.pp_container li {
	list-style: none;
}
.pp_node {
	background: url(\'data:image/gif;base64,R0lGODlhEgASAIABAHJycv///yH5BAEAAAEALAAAAAASABIAAAIejB+Ay6YNU4RvrmoPzpJr/4EduGWldU5ptFLi6LUFADs=\') repeat-y;
	margin-left: 18px;
	zoom: 1;
}
.pp_isLast {
	background: url(\'data:image/gif;base64,R0lGODlhEgASAIABAHJycv///yH5BAEAAAEALAAAAAASABIAAAIYjB+Ay6YNU4RvrmoPzpJr/4HiSJbmiaYFADs=\') no-repeat;
}
.pp_isRoot {
	margin-left: 0;
	background: none;
}
.pp_expandOpen .pp_expand {
	background: url(\'data:image/gif;base64,R0lGODlhEgASAMZFAAAAAHmWwURERDk5OYGcxfz8/Pr6+uLm7P///v7+/e7w84GdxfP09u/x9PX29+ns8Pz8+/79/crT4Pn6+uXo7ufq77rG183V4fDy9MXP3ezu8vHz9e30/tbc5vf3+Ovu8fb3+Obq7+zv8sTO3Ojr79vg6O3v8uPn7cTN3P7+/+Xp7n2axN3i6vj5+YCcxd7j6tzh6ff4+PLz9YGcxPHy9NTb5d/k6/v7+/n5+dLZ5P3+/77J2dXb5b/J2evt8czU4dPa5PT19uTo7cvU4P7+/v////n5+vT199rf6Ort8f///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH5BAEAAH8ALAAAAAASABIAAAeFgH+Cg4SFhoeIiYqLiAGOj5ABhwEWPRkXHTYqPg0zkzsjPzwvFEkNQQSTKEM1LEIPCgwxqYYBEkAwJ7AMHga0hQE5JQPEAwIGBQsCy8uCAUgHJCYyIBMFRAuTBxUiGw5GBQlFv4QBIRo0DjgQCQg6LpMfGEctNxEIKRwrk5GRjP8AAxYKBAA7\');
}
.pp_expandClosed .pp_expand {
	background: url(\'data:image/gif;base64,R0lGODlhEgASAMZDAAAAAHmWwURERDk5OYGcxfz8/Pr6+v///uLm7P7+/YGdxfP09u/x9O7w88rT4P79/fHz9brG1/n6+vz8++fq7/Dy9MXP3czU4c3V4e30/tbc5uzu8uXo7vf3+Ovu8fb3+Obq7+zv8sTO3Ojr79vg6O3v8uPn7cTN3P7+/+Xp7n2axICcxYGcxPn5+fj5+ff4+N7j6t3i6tLZ5N/k6/X29+ns8NTb5f3+//v7+/Hy9NXb5b/J2fT1977J2dPa5PT19uvt8drf6P7+/v///9zh6cvU4Ort8fn5+v///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH5BAEAAH8ALAAAAAASABIAAAeFgH+Cg4SFhoeIiYqLiAGOj5ABhwEROxYYGjMpQAwskz0iFzowHEYMPwSTJ0U2MQM1DQsvqYYBDj5EJgOxHQa0hQEyJAPEAwIGBQoCy8uCAUEIIyUDHxIFQgqTCBQhEAJHBQlDv4QBIBs5NC0TCQc3K5MeFTwuOA8HKBkqk5GRjP8AAxYKBAA7\');
}
.pp_expandLeaf .pp_expand {
	background: url(\'data:image/gif;base64,R0lGODlhEgASAKECAAAAAHJycv///////yH5BAEAAAAALAAAAAASABIAAAIVhI+py+0Po5xUhjuv3lr1CobiSFYFADs=\');
}
.pp_content {
	min-height: 18px;
	margin-left: 18px;
}
* html .pp_content {
	height: 18px;
}
.pp_expand {
	width: 18px;
	height: 18px;
	float: left;
}
.pp_expandOpen .pp_container {
	display: block;
}
.pp_expandClosed .pp_container {
	display: none;
}
.pp_expandOpen .pp_expand, .pp_expandClosed .pp_expand {
	cursor: pointer;
}
.pp_expandLeaf .pp_expand {
	cursor: auto;
}
.pp_wrap {
	font: normal 14.3px/1.4 monospace;
	margin: 0 0 10px 0;
	padding: 5px 10px;
	background: #FFF;
	border: 1px solid #000;
}
.pp_fixed {
	height: 480px;
	width: 640px;
	overflow: auto;
	margin: -240px 0 0 -320px;
	position: fixed;
	left: 50%;
	top: 50%;
	z-index: 9999;
}
.pp_mark {
	padding: 0;
	font-weight: bold;
}
.pp_ctrl {
	font-style: normal;
	cursor: pointer;
}
.pp_ctrlCollapseCh {
	padding: 0 0 0 .1em;
}
.pp_key {
	font-weight: bold;
}
.pp_scope {
	font-style: italic;
}
.pp_scope_public{
	color: #724ADC
}
.pp_scope_private {
	color: red;
}
.pp_scope_protected {
	color: gray;
}
.pp_bool, .pp_null {
	font-style: italic;
	color: #b5b326;
}
.pp_empty {
	font-style: italic;
	color: #aaa;
}
.pp_num {
	color: #19869e;
}
.pp_string {
	color: #555;
}
.pp_resource {
	color: #74169b;
}
.pp_object {
	color: #cc121a;
}
.pp_array {
	color: #121acc;
}
.pp_search {
	margin-right: 1em;
}
.pp_found_in_text {
	background: #38d878;
	font: inherit;
	color: #000;
}
</style>';

	}

}


function pp(){

	$options = func_get_args();
	$val = array_shift( $options );  //  trim first argument

	//  crazy thing to call constructor with variable arguments number
	$reflection = new ReflectionClass( 'nf_pp' );
	$pp = $reflection->newInstanceArgs( $options );

	$pp->pp( $val );
	unset( $pp, $reflection, $val, $options );

}