<?php
/*
$filename - file path to captcha
$apikey   - account's API key
$rtimeout - delay between captcha status checks
$mtimeout - captcha recognition timeout

$is_verbose - false(commenting OFF),  true(commenting ON)

additional custom parameters for each captcha:
$is_phrase - 0 OR 1 - captcha has 2 or more words
$is_regsense - 0 OR 1 - captcha is case sensetive
$is_numeric -  0 OR 1 - captcha has digits only
$min_len    -  0 is no limit, an integer sets minimum text length
$max_len    -  0 is no limit, an integer sets maximum text length
$is_russian -  0 OR 1 - with flag = 1 captcha will be given to a Russian-speaking worker

usage examples:
$text=recognize("/path/to/file/captcha.jpg","YOUR_KEY_HERE",true, "antigate.com");

$text=recognize("/path/to/file/captcha.jpg","YOUR_KEY_HERE",false, "antigate.com");  

$text=recognize("/path/to/file/captcha.jpg","YOUR_KEY_HERE",false, "antigate.com",1,0,0,5);  

*/



function recognize(
            $filename,
            $apikey,
            $is_verbose = true,
            $domain="antigate.com",
            $rtimeout = 5,
            $mtimeout = 120,
            $is_phrase = 0,
            $is_regsense = 0,
            $is_numeric = 0,
            $min_len = 0,
            $max_len = 0,
            $is_russian = 0
            )
{
	if (!file_exists($filename))
	{
		if ($is_verbose) echo "file $filename not found\n";
		return false;
	}
    $postdata = array(
        'method'    => 'post', 
        'key'       => $apikey, 
        'file'      => '@'.$filename, //������ ���� � �����
        'phrase'	=> $is_phrase,
        'regsense'	=> $is_regsense,
        'numeric'	=> $is_numeric,
        'min_len'	=> $min_len,
        'max_len'	=> $max_len,
        
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,             "http://$domain/in.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,     1);
    curl_setopt($ch, CURLOPT_TIMEOUT,             60);
    curl_setopt($ch, CURLOPT_POST,                 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,         $postdata);
    $result = curl_exec($ch);
    if (curl_errno($ch)) 
    {
    	if ($is_verbose) echo "CURL returned error: ".curl_error($ch)."\n";
        return false;
    }
    curl_close($ch);
    if (strpos($result, "ERROR")!==false)
    {
    	if ($is_verbose) echo "server returned error: $result\n";
        return false;
    }
    else
    {
        $ex = explode("|", $result);
        $captcha_id = $ex[1];
    	if ($is_verbose) echo "captcha sent, got captcha ID $captcha_id\n";
        $waittime = 0;
        if ($is_verbose) echo "waiting for $rtimeout seconds\n";
        sleep($rtimeout);
        while(true)
        {
            $result = file_get_contents("http://$domain/res.php?key=".$apikey.'&action=get&id='.$captcha_id);
            if (strpos($result, 'ERROR')!==false)
            {
            	if ($is_verbose) echo "server returned error: $result\n";
                return false;
            }
            if ($result=="CAPCHA_NOT_READY")
            {
            	if ($is_verbose) echo "captcha is not ready yet\n";
            	$waittime += $rtimeout;
            	if ($waittime>$mtimeout) 
            	{
            		if ($is_verbose) echo "timelimit ($mtimeout) hit\n";
            		break;
            	}
        		if ($is_verbose) echo "waiting for $rtimeout seconds\n";
            	sleep($rtimeout);
            }
            else
            {
            	$ex = explode('|', $result);
            	if (trim($ex[0])=='OK') return trim($ex[1]);
            }
        }
        
        return false;
    }
}
