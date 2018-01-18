<?php
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
$site_name=CMainPage::GetSiteByHost();
header('Content-type:text/html; charset=utf-8');
@set_time_limit(0);
@ini_set('memory_limit', '256M');
define('WXR_VERSION', '1.0');
function cackle_identifier_for_post($post) {
    return $post->ID . ' ' . $post->guid;
}

function cackle_export_wxr_missing_parents($categories) {
    if (!is_array($categories) || empty($categories))
        return array();
    foreach ($categories as $category)
        $parents[$category->term_id] = $category->parent;
    $parents = array_unique(array_diff($parents, array_keys($parents)));
    if ($zero = array_search('0', $parents))
        unset($parents[$zero]);
    return $parents;
}

function cackle_export_wxr_cdata($string) {
    global $cackle_api, $site_id;
    //$encoding = mb_detect_encoding($string, array('UTF-8', 'Windows-1251'));
    if ($cackle_api->cackle_get_param("cackle_encoding_".$site_id) == "1"){

        $string = iconv('cp1251', 'utf-8', $string);
    }
    $string = str_replace("<br />","\r\n",$string);
    return $string;
}

function cackle_export_wxr_site_url() {
    global $current_site;
    if (isset($current_site->domain)) {
        return 'http://' . $current_site->domain . $current_site->path;
    } else {
        return get_bloginfo_rss('url');
    }
}



function cackle_export_wp($post, $url,$post_id) {

    global $DB;
    $comments_query = "SELECT * FROM ".PREFIX."_forum_message WHERE PARAM2 = $post_id order by POST_DATE asc";
    $comments = $DB->Query($comments_query);


    ob_start();
    echo '<?xml version="1.0" encoding="utf-8"?' . ">\n";
    ?>
<rss version="2.0"
     xmlns:excerpt="http://wordpress.org/export/1.0/excerpt/"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:cackle="http://cackle.me/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:wp="http://wordpress.org/export/1.0/"
        >

    <channel>
        <item>
            <title>test1</title>

            <link><?php echo $url ?></link>
            <wp:post_id><?php print_r($post) ; ?></wp:post_id>
            <?php
            if ($comments) {
                while ($c=$comments->Fetch()) {
                    ?>
                    <wp:comment>
                        <wp:comment_id><?php echo $c[ID]; ?></wp:comment_id>
                        <wp:comment_author><?php echo cackle_export_wxr_cdata($c[AUTHOR_NAME]); ?></wp:comment_author>
                        <wp:comment_author_email><?php echo $c[AUTHOR_EMAIL]; ?></wp:comment_author_email>
                        <wp:comment_author_url>0 </wp:comment_author_url>
                        <wp:comment_author_IP><?php echo $c[AUTHOR_IP]; ?></wp:comment_author_IP>
                        <wp:comment_date><?php echo $c[POST_DATE]; ?></wp:comment_date>
                        <wp:comment_date_gmt><?php echo $c[POST_DATE]; ?></wp:comment_date_gmt>
                        <wp:comment_content><![CDATA[<?php echo cackle_export_wxr_cdata($c[POST_MESSAGE]) ?>]]></wp:comment_content>
                        <wp:comment_approved><?php $c[APPROVED]=="Y" ? print_r("1") : print_r("0"); ?></wp:comment_approved>
                        <wp:comment_type>0</wp:comment_type>
                        <wp:comment_parent>0</wp:comment_parent>
                    </wp:comment>
                    <?php
                }
            } // comments ?>
        </item>
    </channel>
</rss>
<?php
    $output = ob_get_clean();
    return $output;
}

?>
