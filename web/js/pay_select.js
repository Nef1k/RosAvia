/**
 * Created by 166878 on 14.11.2016.
 */

function GetCertTable(){
    var cert_status = 4;
    var user_id= $("#cert_table").attr("data-id-user");
    var percent = parseInt($("#cert_table").attr("data-user-percent"));
    if (isNaN(percent)){
        percent = 0
    }
    console.log(cert_status, user_id, percent);
    var fields = JSON.stringify(["ID_Sertificate","user_id", "user_login", "name", "last_name", "phone_number", "flight_type", "price"]);
    var criteria = JSON.stringify({ "ID_SertState": [cert_status], "ID_User" : [user_id]});
    var sort = JSON.stringify({"ID_Sertificate":["ASC"]});
    jQuery.post("/certificate/select", {field_names : fields, criteria : criteria, sort : sort}, function (data) {
        console.log(data);
        data.forEach(function (item) {
            var name = "", last_name = "", phone_number = "";
            var flight_type = "", price = 0;
            if (item.name){
                name = item.name;
            }
            if (item.last_name){
                last_name = item.last_name;
            }
            if (item.phone_number){
                phone_number = item.phone_number;
            }
            if (item.flight_type){
                flight_type = item.flight_type;
            }
            if (item.price){
                price= parseInt(item.price)*(1-percent/100);
            }
            $("#cert_table").append(
                "<tr class='certificate_row' data-id='' style='cursor:pointer;'  onclick='mark(this)'>" +
                    "<td><input type='checkbox' autocomplete='off' class='cert_checkbox' data-cert_id='"+item.ID_Sertificate+"'></td>"+
                    "<th>"+item.ID_Sertificate+"</th>" +
                    "<td>" + name + " " + last_name + "</td>" +
                    "<td>" + phone_number + "</td>" +
                    "<td><b>" + price + " р.</b></td>" +
                    "<td>" + flight_type + "</td>" +
                "</tr>"
            )
        });
    })
}

function mark_all() {
    $(".cert_checkbox").prop("checked",true);
}

function unmark_all() {
    $(".cert_checkbox").prop("checked",false);
}

function mark(data) {
    var box = $(data.firstChild.firstChild);
    if (box.prop("checked")){
        box.prop("checked",false)
    }
    else{
        box.prop("checked", true)
    }
}

$(document).ready(function (event) {
    GetCertTable();
});
