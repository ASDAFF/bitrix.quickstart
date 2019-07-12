$(document).ready(function () {
    $("#other").hide();
    var val = $("select.span12 option:selected").val();
    if(val == 14)
        $("#other").show();
    $("select.span12").change(function(){
        var val = $("select.span12 option:selected").val();
        if(val == 14)
            $("#other").show();
        else
            $("#other").hide();
    });
    
    var error = $("#errors").children("p").size();
    if(!error)
        $("#driver_form .modal-body").hide();
        
    $("#driver_form .modal-header a").click(function(){
        $("#driver_form .modal-body").toggle("normal");
    })
})