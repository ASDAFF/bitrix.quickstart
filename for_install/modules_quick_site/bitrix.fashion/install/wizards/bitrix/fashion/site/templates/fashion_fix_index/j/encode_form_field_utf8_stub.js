// encode_form_field_utf8_stub.js : Universal URL Encoder (Stub for UTF8 encoding pages)
// Provides "encodeFormField" function (in this stub mapped to "encodeURIComponent")
// USE THIS FILE ON UTF8 PAGES! For non-UTF8 pages use encode_form_field.js
// Free permission to use granted under BSD licence
// Written by Serge Ageyev <justboss777@gmail.com>
// Version 1.2 2010 SA: Initial implemenation as stub for utf8

/**
  * Encodes text value in current browser codepage
  * like regular POST field form encoded when FORM tags posted
  * You can use this function instead of encodeURIComponent
  * Note: Maintained here for compatibility only, mapped to encodeURIComponent
  * @param {String} text Text to encode
  * @param {function} fallbackFunc [Optional] to be called to translate chars that not exist in current codepage
  * @return text param encoded like POST form param
  * @type String
  */

var encodeFormField = function(text, fallbackFunc)
 {
  return(encodeURIComponent(text));
 }

// Internal implemenation class

var encodeFormFieldProc = 
 {
  XlateLine : ''
  ,

  encode : function(text, fallbackFunc)
   {
    return(encodeURIComponent(text));
   }
   ,

  encodeDefaultFallbackFunc : function(charToEncode)
   {
    return(encodeURIComponent(charToEncode.charAt(0)));
   }
   ,

  /**
    * Detects UTF8 encoding. Internal use only.
    * @return true if current encoding is UTF8
    * @type Boolean
    */

  isPageOnUTF8 : function()
   {
    var pattern = 'А'; // Single unicode character in UTF8 (cyrillic letter A)

    if (pattern.length == 1)
     {
      return(true); // Converted to single character - UTF8
     }
    else
     {
      return(false);
     }
   }
 };

if (!encodeFormFieldProc.isPageOnUTF8())
 {
  // Invalid context: Script must be used on UTF8 pages!
  throw('Script [encode_form_field_utf8_stub.js] is designed for UTF8 pages only, use [encode_form_field.js] on non-UTF8 pages');
 }
