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
    var criteria = JSON.stringify({ "ID_SertState": [cert_status], "ID_User" : [user_id], "ID_Certificate_Pack":[null]});
    var sort = JSON.stringify({"ID_Sertificate":["ASC"]});
    jQuery.post("/certificate/select", {field_names : fields, criteria : criteria, sort : sort}, function (data) {
        console.log(data);
        if (data.length == 0){
            $("#cert_table").append(
                "<tr><td class='text-center certificate-row' colspan='6'><h3>Подходящих сертификатов нет</h3></td></tr>"
            );
        }
        else {
            data.forEach(function (item) {
                $(".table-loader").addClass("hidden");
                var name = "", last_name = "", phone_number = "";
                var flight_type = "", price = 0;
                if (item.name) {
                    name = item.name;
                }
                if (item.last_name) {
                    last_name = item.last_name;
                }
                if (item.phone_number) {
                    phone_number = item.phone_number;
                }
                if (item.flight_type) {
                    flight_type = item.flight_type;
                }
                if (item.price) {
                    price = parseInt(item.price) * (1 - percent / 100);
                }
                $("#cert_table").append(
                    "<tr class='certificate_row' data-id='' style='cursor:pointer;'  onclick='mark(this)'>" +
                    "<td><input type='checkbox' autocomplete='off' class='cert_checkbox' data-cert_id='" + item.ID_Sertificate + "'></td>" +
                    "<th>" + item.ID_Sertificate + "</th>" +
                    "<td>" + name + " " + last_name + "</td>" +
                    "<td>" + phone_number + "</td>" +
                    "<td><b>" + price + " р.</b></td>" +
                    "<td>" + flight_type + "</td>" +
                    "</tr>"
                )
            });
        }
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

function PayBtnClick() {
    var checked_certs = [];
    var selector = $("input:checked");
    var payment_method = $("#payment_method").val();
    var yesNoDialog = new YesNoDialog;
    yesNoDialog.setModalSelector("#yes-no-modal");
    yesNoDialog.show({
        caption: "Подтвердите оплату",
        yes_caption: "Ок",
        no_caption: "Отмена",
        yes_handler: NoBtn,
        no_handler: NoBtn
    });
    yesNoDialog.showLoader();
    var msg = "<div class='text-center'>Проверьте список оплачиваемых сертификатов:</div>";
    for (var i = 0; i<selector.length; ++i){
        checked_certs[i] = parseInt($(selector[i]).attr("data-cert_id"));
    }
    console.log(selector);
    console.log(checked_certs);
    console.log($("#payment_method").val());
    $("#yes-no-modal div").removeClass("modal-sm");
    $("#yes-no-modal div").addClass("modal-lg");

    if (checked_certs.length == 0){
        msg="<div class='text-center'>Сертификаты не выбраны.</div>";
        yesNoDialog.message = msg;
        yesNoDialog.hideLoader();
        yesNoDialog.applyParams();
    }
    else {
        var percent = parseInt($("#cert_table").attr("data-user-percent"));
        if (isNaN(percent)){
            percent = 0
        }
        var fields = JSON.stringify(["ID_Sertificate", "name", "last_name", "phone_number", "flight_type", "price"]);
        var criteria = JSON.stringify({ "ID_Sertificate": [checked_certs]});
        var sort = JSON.stringify({"ID_Sertificate":["ASC"]});
        jQuery.post("/certificate/select", {field_names : fields, criteria : criteria, sort : sort}, function (data) {
            console.log(data);
            msg += "<table class='table table-hover table-striped'>" +
                        "<tr>"+
                            "<th>ID</th>"+
                            "<th class='col-md-4'>Клиент</th>"+
                            "<th class='col-md-3'>Телефон</th>"+
                            "<th class='col-md-2'>Цена</th>"+
                            "<th class='col-md-3'>Тип полёта</th>"+
                        "</tr>";
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
                msg +=  "<tr class='certificate_row' data-id='' style='cursor:pointer;'>" +
                            "<th>"+item.ID_Sertificate+"</th>" +
                            "<td>" + name + " " + last_name + "</td>" +
                            "<td>" + phone_number + "</td>" +
                            "<td><b>" + price + " р.</b></td>" +
                            "<td>" + flight_type + "</td>" +
                        "</tr>";
            });
            msg += "</table>";
            yesNoDialog.message = msg;
            yesNoDialog.hideLoader();
            yesNoDialog.applyParams();
            yesNoDialog.yes_handler = YesBtn(checked_certs);
        })
    }
}

function YesBtn(event, data) {
    var modal = event.data;
    console.log(data);
    modal.close();
}
function NoBtn() {
    var modal = event.data;
    modal.close();
}
$(document).ready(function (event) {
    GetCertTable();
});
