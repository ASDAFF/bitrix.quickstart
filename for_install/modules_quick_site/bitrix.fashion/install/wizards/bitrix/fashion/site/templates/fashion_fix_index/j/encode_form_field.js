// encode_form_field.js : Universal URL Encoder (Warning: use for non-UTF8 pages only)
// Encodes UNICODE text value in current browser codepage like regular POST field form encoded when FORM tags is posted.
// Allows easy use non-ascii characters in AJAX requests to PHP for any single-byte encodings [cp1251, cp1252,  Latin-1/ISO 8859-1, etc]
// Provides "encodeFormField" function (drop-in replacement for "escape" and/or "encodeURIComponent")
// Notes:
//  * DO NOT USE THIS FILE ON UTF8 PAGES! Use encode_form_field_utf8_stub.js when your page in UTF8
//  * Avoid Copy-n-Paste of this file, since most editors with break the encode xlate line
//  * This file must be loaded in same codepage as code that use it (usually its true, but you have to know it)
// Free permission to use granted under BSD licence
// Written by Serge Ageyev <justboss777@gmail.com>
// Version 1.0 2009 SA: Initial implementation
// Version 1.1 2010 SA: encodeFormFieldDefaultFallback function support is added
// Version 1.2 2010 SA: Added exception if script is used on UTF8 pages, added encode_form_field_utf8_stub.js to library

/**
  * Encodes text value in current browser codepage
  * like regular POST field form encoded when FORM tags posted
  * You can use this function instead of encodeURIComponent
  * @param {String} text Text to encode
  * @param {function} fallbackFunc [Optional] to be called to translate chars that not exist in current codepage
  * @return text param encoded like POST form param
  * @type String
  */

var encodeFormField = function(text, fallbackFunc)
 {
  return(encodeFormFieldProc.encode(text, fallbackFunc));
 }

// Internal implemenation class

var encodeFormFieldProc = 
 {
  /**
    * Translation line: Main idea - Force browser Jscript engile to convert this to unicode using current page to build translation map
    */

  XlateLine : 
  // DO NOT EDIT THE LINE BELLOW! It MUST contains single-byte characters with codes from 0x080 to 0xFF (Note: be careful with Copy-n-Paste!) 
  '€‚ƒ„…†‡ˆ‰Š‹Œ‘’“”•–—˜™š›œŸ ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖ×ØÙÚÛÜİŞßàáâãäåæçèéêëìíîïğñòóôõö÷øùúûüışÿ'
  ,

  /**
    * Encodes text value in current browser codepage
    * like regular POST field form encoded when FORM tags posted
    * You can use this function instead of encodeURIComponent
    * @param {String} text Text to encode
    * @param {function} fallbackFunc [Optional] to be called to translate chars that not exist in current codepage
    * @return text param encoded like POST form param
    * @type String
   */

  encode : function(text, fallbackFunc)
   {
    if (this.isPageOnUTF8())
     {
      // Invalid context: We must not be called on UTF8 pages!
      throw('Script [encode_form_field.js] is designed for non-UTF8 pages only, use [encode_form_field_utf8_stub.js] on UTF8');
      //return(encodeURIComponent(text));
     }

    if (fallbackFunc == null)
     {
      // If no fallback is defined, use current default
      fallbackFunc = this.encodeDefaultFallbackFunc;
     }

    text = ''+text; // Force text to be text

    var len = text.length;

    var result = '';

    var pos,text_char;

    for (var i = 0; i < len; i++)
     {
      text_char = text.charAt(i);

      if (text_char.charCodeAt(0) < 0x80)
       {
        result += escape(text_char);
       }
      else
       {
        pos = this.XlateLine.indexOf(text_char);

        if (pos >= 0)
         {
          result += '%'+(pos+0x80).toString(16).toUpperCase();
         }
        else
         {
          result += ''+fallbackFunc(text_char);
         }
       }
     }

    return(result);
   }
   ,


  /**
    * Default fallback function to be called to translate chars that not exist in current codepage
    * @param {String} charToEncode UNICODE character not found in current CP to encode
    * @return string represents encoded text to send instead of original character
    * @type String
    */

  encodeDefaultFallbackFunc : function(charToEncode)
   {
    // if incoming unicode character is not in current codepage, you have to translate it somehow
    // Examples of possible fallbacks is here:

    // return(escape(text_char)); // Send char as unicode %uXXXX
    // return('');                // Ignore this char
    // return(escape('?'));       // Send encoded question char
    return('%26%23'+charToEncode.charCodeAt(0)+'%3B'); // Send &#{code}; [most browsers do this way on FORM encode]
   }
   ,

  /**
    * Detects UTF8 encoding. Internal use only.
    * Note: Do not use this script on UTF8 pages!
    * @return true if current encoding is UTF8
    * @type Boolean
    */

  isPageOnUTF8 : function()
   {
    var pattern = 'Ğ'; // Single unicode character in UTF8 (cyrillic letter A)

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

if (encodeFormFieldProc.isPageOnUTF8())
 {
  // Invalid context: Script must not be used on UTF8 pages!
  throw('Script [encode_form_field.js] is designed for non-UTF8 pages only, use [encode_form_field_utf8_stub.js] on UTF8');
 }
