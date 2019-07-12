<?
class Translit
{
    function Transliterate($string)
    {
        $cyr = array(
                "Ù",  "Ø", "×", "Ö","Þ", "ß", "Æ", "À","Á","Â","Ã","Ä","Å","¨","Ç","È","É","Ê","Ë","Ì","Í","Î","Ï","Ð","Ñ","Ò","Ó","Ô","Õ", "Ü","Û","Ú","Ý","ª","¯",
                "ù",  "ø", "÷", "ö","þ", "ÿ", "æ", "à","á","â","ã","ä","å","¸","ç","è","é","ê","ë","ì","í","î","ï","ð","ñ","ò","ó","ô","õ", "ü","û","ú","ý","º","¿"
        );
        $lat = array(
                "Shh","Sh","Ch","C","Ju","Ja","Zh","A","B","V","G","D","Je","Jo","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","Kh","'","Y","`","E","Je","Ji",
                "shh","sh","ch","c","ju","ja","zh","a","b","v","g","d","je","jo","z","i","j","k","l","m","n","o","p","r","s","t","u","f","kh","'","y","`","e","je","ji"
        );
        for ($i=0; $i < count($cyr); $i++)
        {
            $c_cyr = $cyr[$i];
            $c_lat = $lat[$i];
            $string = str_replace($c_cyr, $c_lat, $string);
        }
        $string = preg_replace("/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]e/", "\${1}e", $string);
        $string = preg_replace("/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]/", "\${1}'", $string);
        $string = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $string);
        $string = preg_replace("/^kh/", "h", $string);
        $string = preg_replace("/^Kh/", "H", $string);
        return $string;
    }
    function UrlTranslit($string)
    {
        $string = preg_replace("/[_\s\.,?!\[\](){}]+/", "-", $string);
        $string = preg_replace("/-{2,}/", "--", $string);
        $string = preg_replace("/_-+_/", "--", $string);
        $string = preg_replace("/[_\-]+$/", "", $string);
        $string = Translit::Transliterate($string);
        $string = ToLower($string);
        $string = preg_replace("/j{2,}/", "j", $string);
        $string = preg_replace("/[^0-9a-z_\-]+/", "-", $string);
        return $string;
    }
}
?>