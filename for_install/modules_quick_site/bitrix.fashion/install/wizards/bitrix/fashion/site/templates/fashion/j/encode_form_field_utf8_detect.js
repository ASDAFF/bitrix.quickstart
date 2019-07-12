// encode_form_field_utf8_detect.js : Detects is page loaded on UTF-8 encoding
// Provides "encodeFormFieldIsPageOnUTF8" function
// Version 1.2 2010 SA: Initial implemenation as for autodetect code for encode_form_field.js

/**
  * Detects UTF8 encoding. Internal use only.
  * @return true if current encoding is UTF8
  * @type Boolean
  */

var encodeFormFieldIsPageOnUTF8 = function()
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
