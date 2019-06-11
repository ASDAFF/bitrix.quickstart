$(document).ready(function () {
    var a = $("#pagination-container");
    a.on("click", "a.more_goods", function () {
        var d = a.find("div.bx-pagination  ul li.bx-pag-next a").attr("href");
        var b = $(this).html();
        var c = this;
        $(this).html("...");
        if (d !== undefined) {
            $.ajax({
                type: "POST", url: d, data: {ajax_get_page: "y"}, dataType: "html", success: function (f) {
                    var e = $(f).find(".pagination-items").find(".row-item");
                    var g = $(f).find(".pages-container").html();
                    a.find(".pagination-items").append(e);
                    a.find(".pages-container").html(g);
                    history.pushState("", "", d);
                    $(c).html(b)
                }
            })
        }
        return false
    })
});