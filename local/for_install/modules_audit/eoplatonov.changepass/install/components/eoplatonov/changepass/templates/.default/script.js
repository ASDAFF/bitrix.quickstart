$(document).ready(function(){
    $(".changepass form").submit(submit_changepass);
});
function submit_changepass(){
    $.post("",$(this).serialize(),function(d){
        $(".changepass").html(d);
        $(".changepass form").submit(submit_changepass);
    });
    return false;
}