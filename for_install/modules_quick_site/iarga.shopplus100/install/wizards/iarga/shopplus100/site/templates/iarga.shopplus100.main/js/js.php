<?php 
  header('Content-type: text/javascript; charset=utf-8'); 
  ob_start("compress"); 
  function compress($buffer) { 
	  $buffer = str_replace("#SITE"."_DIR#","#SITE_DIR#",$buffer);
    /* удалить комментарии */
   // $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); 
    /* удалить табуляции, пробелы, символы новой строки и т.д. */
   // $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer); 
    return $buffer; 
  } 
  /* js файлы */
  @ include('jquery-1.8.2.min.js'); 
  @ include('jquery-ui-1.9.2.custom.min.js');
  @ include('royalslider/jquery.royalslider.min.js');

		
  @ include('base.js');
  @ include('func.js');
  @ include('shop.js');
  @ include('iarga.js');
  @ include('fancybox/source/jquery.fancybox.pack.js');
  ob_end_flush(); 
?>