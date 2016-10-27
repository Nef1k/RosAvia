/**
 * Created by 166878 on 27.10.2016.
 */

function getParams() {
    var userID = $("h1").attr("data-ID_user");
    console.log(userID);
    var user_group_id = $("#userGroup").val();
    jQuery.get("/admin/user_group_edit?user_id="+userID+"&user_group_id="+user_group_id, function (data) {
        $(".loader").addClass("hidden");
        console.log(data);
        data.params.forEach(function (item, i) {
            if (!item.value){
                item.value = ""
            }
            if (!(i%2)){
                $("#params").append("<div class='row'>");
            }

            $("#params .row:last").append(
                "<div class='col-md-6 text-center'>" +
                    "<div class='input-group' id='+ item.id+'>"+
                        "<span class='input-group-addon' id='basic-addon3'><b>"+item.name+":</b></span>"+
                        "<input type='text' class='form-control' aria-label='...' value='"+ item.value+"'>"+
                    "</div>" +
                "</div>"
            );
            if (i%2){
                $("#params").append("</div><br>");
            }
        });
        $("#params br:last").remove(0);
    })
}

function SaveChanges() {
    
}

$(document).ready(function (event) {
   getParams();
});