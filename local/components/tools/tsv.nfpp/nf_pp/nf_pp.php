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
 * 
 * @author MAYDOKIN Aleksey <admin [at] alexxxnf [dot] ru>
 * @version 1.2.3
 */
class nf_pp {

	public
		$trimString    = 100,
		$autoCollapsed = FALSE,
		$autoOpen      = array();
	
	protected static
		$jsFuncDisp = FALSE,
		$cssDisp    = FALSE;
	
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

	function pp( $val, $curLevel = 0, $key = NULL ){
	
		if( $curLevel == 0 ){
			$this->backtrace();
			$md5 = md5( serialize($val).rand() );
			echo '<ul id="pp_'.$md5.'" class="pp_tree">';
		}
			
		echo '<li class="pp_item">';
		
		if( $key !== NULL )
			echo '<span class="pp_key">['.$key.']</span> => ';

		//  determine type of the variable
		switch( gettype( $val ) ){
		
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
				$this->p_array( $val );
				break;
			
			case 'object':
				$this->p_object( $val );
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
			$this->p_jsinit( 'pp_'.$md5 );
		}

	}
	
	
	protected function p_bool( $val ){
	
		echo '<span class="pp_bool">'.strtoupper( var_export( $val, TRUE ) ).'</span>';
	
	}
	
	protected function p_null( $val ){
	
		echo '<span class="pp_null">'.strtoupper( var_export( $val, TRUE ) ).'</span>';
	
	}
	
	protected function p_basic( $val ){
	
		echo '<span class="pp_num">'.$val.'</span>';
	
	}
	
	protected function p_string( $val ){
	
		$val = htmlspecialchars( $val );
		if( $this->trimString > 0 && strlen( $val ) > $this->trimString ){
		
			if( $this->trimString > 3 )
				$val = substr( $val, 0, $this->trimString - 3 ).'...';
			else
				$val = substr( $val, 0, $this->trimString );
			
		}
		
		echo '<span class="pp_string">'.$val.'</span>';
	
	}
	
	protected function p_array( $val ){
	
		$size = sizeof( $val );
	
		echo '<span class="pp_array">Array</span><i class="pp_ctrl pp_ctrlCollapseCh">('.$size.')</i><ul class="pp_subtree">';
		
		if( $size ){
		
			foreach( $val as $k => $v )
				echo $this->pp( $v, $curLevel + 1, htmlspecialchars( $k ) );
				
		}
		else{
		
			echo '<li class="pp_item"><span class="pp_empty">EMPTY</span></li>';
			
		}
		echo '</ul>';
	
	}
	
	protected function p_object( $val ){
	
		$className = get_class( $val );
		$val = (array)$val;
		$size = sizeof( $val );
		
		echo '<span class="pp_object">Object &lt;'.$className.'&gt;</span><i class="pp_ctrl pp_ctrlCollapseCh">('.$size.')</i><ul class="pp_subtree">';
		
		if( $size ){
		
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
				
				echo $this->pp( $v, $curLevel + 1, $k );
				
			}
			
		}
		else{
		
			echo '<li class="pp_item"><span class="pp_empty">EMPTY</span></li>';
			
		}
		
		echo '</ul>';
	
	}
	
	protected function p_res( $val ){
	
		echo '<span class="pp_resource">'.$val.' &lt;'.get_resource_type( $val ).'&gt;</span>';
	
	}
	
	protected function p_unknown( $val ){
	
		echo '<span class="pp_unknown">"'.$val.'"</span>';
	
	}

	/**
	 *  Prints a mark before the tree
	 */
	protected function backtrace(){
	
		$backtrace = debug_backtrace();
		
		if( $backtrace[2]['function'] == 'pp' )
			$arToPrint = $backtrace[2];  //  run as function
		else
			$arToPrint = $backtrace[1];  //  run as method

		echo '<div class="pp_mark">'.$arToPrint['file'].' '.$arToPrint['line'].'</div>';
	
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
		sigMinus        = \'(&minus;)\',
		sigPlus         = \'(+)\',
		sigDiabled      = \'(&nbsp;)\',
		sigCollapseCh   = \'(#num#)\',
		classMinus      = \'pp_ctrlMinus\',
		classPlus       = \'pp_ctrlPlus\',
		classDisabled   = \'pp_ctrlDisabled\',
		classCollapseCh = \'pp_ctrlCollapseCh\',
		classKey        = \'pp_key\',
		classRoot       = \'pp_tree\';


	var tree = document.getElementById( id );
	applyCtrlRec( tree, autoCollapsed );
	
	if( autoOpen.length )
		autoOpenTree( tree, autoOpen );
		
		
	/**
	 *  Recursively adds controls to every LI-node that has UL-child
	 */
	function applyCtrlRec( ul, autoCollapsed ){

		var arLi = ul.children;

		for( var c = arLi.length - 1; c >= 0; c-- ){

			var curLi = arLi[c],
				chUl    = getChildUl( curLi );
			
			if( chUl !== false ){

				addCtrl( curLi, autoCollapsed ? 0 : 1 );
				if( autoCollapsed )
					chUl.style.display = \'none\';
				
				applyCtrlRec( chUl, autoCollapsed );
			
			}
			else{
			
				addCtrl( curLi, 2 );
			
			}
		
		}
	
	}


	/**
	 *  Returns child div or false if not found
	 *  @param li parent node
	 */
	function getChildUl( li ){
	
		var children = li.children;
		
		for( var c = children.length - 1; c >= 0; c-- )
			if( children[c].nodeName == \'UL\' )
				return children[c];
				
		return false;
	
	}
	
	
	/**
	 *  Adds control to current li
	 */
	function addCtrl( li, state ){
	
		switch( state ){
			
			case 0:  //  collapsed
				var sign   = sigPlus,
					actClass = classPlus;
				break;
				
			case 1:  //  opened
				var sign   = sigMinus,
					actClass = classMinus;
				break;
				
			case 2:  //  disabled
				var sign   = sigDiabled,
					actClass = classDisabled;
				break;
		
		}

		var ctrl = document.createElement( \'i\' );
		ctrl.innerHTML = sign;
		ctrl.className = \'pp_ctrl \' + actClass;
		applyHdlr( ctrl, ctrlHandler );

		li.insertBefore( ctrl, li.firstChild );
		
		
		//  collapse children
		if( state == 0 || state == 1 ){
		
			var arI = li.children,
				ctrl = false;

			for( var c = arI.length - 1; c >= 0; c-- ){
				if( arI[c].className.indexOf( classCollapseCh ) != -1 ){
					ctrl = arI[c];
					break;
				}
			}

			if( ctrl );
				applyHdlr( ctrl, collapseChHandler );
			
		}

	}
	
	
	/**
	 *  Adds onClick-handler to control
	 */
	function applyHdlr( target, handler, type ){
	
		type = type || \'click\';

		if (target.addEventListener)
			target.addEventListener( type, handler, false );
		else
			target.attachEvent( \'on\' + type, function(){ handler.call( target ) } );
	
	}
	
	
	/**
	 *  Handler that handles clicks on controls (+/-)
	 */
	function ctrlHandler(){
		
		var ul = getChildUl( this.parentNode ),
			className = this.className;

		if( ul ){
		
			if( this.className.indexOf( classMinus ) >= 0 )
				closeNode( this );
			else
				openNode( this );
				
		}
	
	}
	

	/**
	 *  Handler that handles collapse children
	 */	
	function collapseChHandler(){

		var
			arLi = getChildUl( this.parentNode ).children,
			collapse = false;
		
		//  determine open or collapse children
		for( var c = arLi.length - 1; c >= 0; c-- ){

			if( arLi[c].firstChild.className.indexOf( classMinus ) >= 0 ){
				collapse = true;
				break;
			}
			
		}

		for( var c = arLi.length - 1; c >= 0; c-- ){

			if( getChildUl( arLi[c] ) ){
			
				if( collapse )
					closeNode( arLi[c].firstChild );
				else
					openNode( arLi[c].firstChild );
				
			}
		
		}
	
	}
		
	
	function closeNode( ctrl ){
	
		var ul = getChildUl( ctrl.parentNode ),
			className = ctrl.className;
		
		if( ! ul )
			return;
	
		ul.style.display = \'none\';
		ctrl.className = className.replace( classMinus, classPlus );
		ctrl.innerHTML = sigPlus;
	
	}
	
	
	function openNode( ctrl ){
	
		var ul = getChildUl( ctrl.parentNode ),
			className = ctrl.className;
		
		if( ! ul )
			return;
	
		ul.style.display = \'block\';
		ctrl.className = className.replace( classPlus, classMinus );
		ctrl.innerHTML = sigMinus;
	
	}
	
	
	function autoOpenTree( ul, autoOpen ){
	
		var arSpan = ul.getElementsByTagName( \'SPAN\' ),
			arToOpen = [];
		
		for( var c = arSpan.length - 1; c >= 0; c-- ){
		
			var curSpan = arSpan[c];
			
			if( curSpan.className.indexOf( classKey ) < 0 )
				continue;
				
			for( var q = autoOpen.length - 1; q >= 0; q-- ){
			
				var rx = new RegExp( \'\\\\[\'+autoOpen[q]+\'(:<span[^<]*</span>)?\\\\]\', \'i\' );
				
				if( curSpan.innerHTML.search( rx ) != -1 ){
					arToOpen.push( curSpan.previousSibling );
					break;
				}
			
			}
		
		}
		
		for( var c = arToOpen.length - 1; c >= 0; c-- )
			openNodeRec( arToOpen[c] );
	
	}
	
	
	function openNodeRec( ctrl ){

		openNode( ctrl );
		
		var parent = ctrl.parentNode.parentNode;  //  parent ul
		
		if( parent.className == classRoot )
			return;
			
		ctrl = parent.parentNode.firstChild;
		openNodeRec( ctrl );
	
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

	.pp_tree, .pp_mark {
		font: normal 110%/1.4 monospace;
		margin: 0 0 10px 0;
		padding: 0;
	}
	.pp_mark {
		font-weight: bold;
		margin: 0;
	}
	.pp_subtree {
		padding: 0;
		margin: 0 0 10px 20px;
	}
	.pp_item {
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.pp_ctrl {
		font-style: normal;
		cursor: pointer;
	}
	.pp_ctrlDisabled {
		visibility: hidden;
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