<?php 
  header('Content-type: text/css'); 
  ob_start("compress"); 
  function compress($buffer) { 
    /* удалить комментарии */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); 
    /* удалить табуляции, пробелы, символы новой строки и т.д. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer); 
    return $buffer; 
  } 
  /* css файлы */
  @ include('base.css'); 
  @ include('media-queries.css'); 
  @ include('../js/royalslider/default/rs-default.css'); 
  @ include('../js/royalslider/royalslider.css'); 
  @ include('iarga.css');
  ob_end_flush(); 
?>