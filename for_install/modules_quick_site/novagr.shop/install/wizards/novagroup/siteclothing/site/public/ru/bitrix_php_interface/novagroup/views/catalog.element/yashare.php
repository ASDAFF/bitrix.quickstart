<div class="addthis_toolbox addthis_default_style">
    <a class="addthis_button_vk"></a>
    <a class="addthis_button_facebook"></a>
    <a class="addthis_button_google_plusone_share"></a>
    <a class="addthis_button_pinterest_share"></a>
</div>

<script>
    var addthis_config = {
        image_exclude: "at_exclude"
    }
    function setAddThisSettings()
    {
        var domain = window.document.location.protocol + "//" + window.document.location.host;
        var path = domain + window.document.location.pathname;
        var search = window.document.location.search;
        var imgURL = "";
        var aID = ".detailLink:visible";

        //if the detail cart
        if($.trim($(aID+" img").attr("data-big-pic"))!="")
        {
            //если доступен data-big-pic
            imgURL = $(aID+" img").attr("data-big-pic");
        } else {
            var href = $.trim($(aID).attr("href"));
            var n = href.search(/\./i);
            if(n>0)
            {
                //если есть намек на адрес картинки в ссылке #fLinkPic, то выбираем эту ссылку
                imgURL = $(aID).attr("href");
            } else {
                //если не найдены ни data-big-pic ни href
                imgURL = $(aID+" img").attr("src");
            }
        }
        var url = path + "?image="+ imgURL +"&"+search.substr(1) + window.document.location.hash;
        addthis.toolbox('.addthis_toolbox', {}, {'url': url});
    }
    setInterval(setAddThisSettings,500);
</script>