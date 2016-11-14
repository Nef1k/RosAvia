/**
 * Created by 166878 on 14.11.2016.
 */

function GetCertTable(){
    var cert_status = 4;
    var user_id= $("#cert_table").attr("data-id-user");
    var percent = parseInt($("#cert_table").attr("data-user-percent"));
    if (isNaN(percent) || (percent<0) || (percent>100)){
        percent = 0
    }
    var fields = JSON.stringify(["ID_Sertificate", "name", "last_name", "phone_number", "flight_type", "price"]);
    var criteria = JSON.stringify({ "ID_SertState": [cert_status], "ID_User" : [user_id], "ID_CertificatePack":[null]});
    var sort = JSON.stringify({"ID_Sertificate":["ASC"]});

    jQuery.post("/certificate/select", {field_names : fields, criteria : criteria, sort : sort}, function (data) {
        console.log(data);
        $(".table-loader").addClass("hidden");
        if (data.length == 0){
            $("#cert_table").append(
                "<tr><td class='text-center certificate-row' colspan='6'><b>Подходящих сертификатов нет</b></td></tr>"
            );
        }
        else {
            data.forEach(function (item) {
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
                    "<tr class='certificate_row' style='cursor:pointer;'  onclick='mark(this);IncreaseTotal(this.firstChild.firstChild)'>" +
                    "<td><input style='cursor:pointer;' onclick='event.cancelBubble=true;' onchange='IncreaseTotal(this)' type='checkbox' autocomplete='off' class='cert_checkbox' data-cert_id='" + item.ID_Sertificate + "' data-price='"+price+"'></td>" +
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

function IncreaseTotal(data) {
    var total_sum = parseInt($("#total-sum").html());
    if ($(data).prop("checked")){
        total_sum += parseInt($(data).attr("data-price"))
    }
    else {
        total_sum -= parseInt($(data).attr("data-price"))
    }
    $("#total-sum").html(total_sum);
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
    var payment_show = payment_method==0?"Карта":"Наличные";
    for (var i = 0; i<selector.length; ++i){
        checked_certs[i] = parseInt($(selector[i]).attr("data-cert_id"));
    }

    var yesNoDialog = new YesNoDialog;
    yesNoDialog.setModalSelector("#yes-no-modal");
    yesNoDialog.show({
        caption: "Подтвердите оплату",
        yes_caption: "Оплатить",
        no_caption: "Отмена",
        data: {checked_certs : checked_certs, method: payment_method, reload: 0},
        yes_handler: YesBtn,
        no_handler: NoBtn
    });
    yesNoDialog.showLoader();
    var msg = "";
    if (checked_certs.length == 0){
        msg="<div class='text-center'>Сертификаты не выбраны.</div>";
        yesNoDialog.yes_handler = NoBtn;
        yesNoDialog.message = msg;
        yesNoDialog.no_caption="";
        yesNoDialog.yes_caption = "Ок";
        yesNoDialog.hideLoader();
        yesNoDialog.applyParams();
    }
    else {
        $(yesNoDialog.modal_selector+">div").removeClass("modal-sm");
        $(yesNoDialog.modal_selector+">div").addClass("modal-lg");
        var percent = parseInt($("#cert_table").attr("data-user-percent"));
        if (isNaN(percent)){
            percent = 0
        }
        var total_sum = 0;
        var fields = JSON.stringify(["ID_Sertificate", "name", "last_name", "phone_number", "flight_type", "price"]);
        var criteria = JSON.stringify({ "ID_Sertificate": checked_certs});
        var sort = JSON.stringify({"ID_Sertificate":["ASC"]});
        jQuery.post("/certificate/select", {field_names : fields, criteria : criteria, sort : sort}, function (data) {
            console.log(data);
            msg += "<table class='table table-striped'>" +
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
                total_sum += price;
                msg +=  "<tr class='certificate_row' data-id='' style='cursor:pointer;'>" +
                            "<th>"+item.ID_Sertificate+"</th>" +
                            "<td>" + name + " " + last_name + "</td>" +
                            "<td>" + phone_number + "</td>" +
                            "<td><b>" + price + " р.</b></td>" +
                            "<td>" + flight_type + "</td>" +
                        "</tr>";
            });
            msg += "</table>" +
                "<div class='alert alert-info' role='alert'><b>Сумма к оплате:<span class='pull-right'>"+total_sum+" рублей</b></span></div>" +
                "<div class='alert alert-info' role='alert'><b>Способ оплаты:<span class='pull-right'>"+payment_show+"</b></span></div>";
            yesNoDialog.message = msg;
            yesNoDialog.hideLoader();
            yesNoDialog.applyParams();

        })
    }
}

function YesBtn(event) {
    var modal = event.data;
    modal.no_caption="";
    modal.message="";
    modal.yes_caption = "Ок";
    $(modal.modal_selector+">div").addClass("modal-sm");
    $(modal.modal_selector+">div").removeClass("modal-lg");
    modal.showLoader();
    modal.yes_handler = NoBtn;
    modal.applyParams();
    var payment_method = modal.data.method;
    var checked_certs = modal.data.checked_certs;
    var params = {
        payment_method: payment_method,
        cert_ids: JSON.stringify(checked_certs)
    };
    jQuery.post("/certificate_pack/create", params, function (data) {
        console.log(data);
        var errors = data.error_msg;
        if (errors.length > 0){
            msg = "Возникли следующие ошибки при выполнении действия: <br>" + errors.join(" <br>")+".";
        }
        else{
            msg = "Оплата пакета сертификатов с номером <b>"+data.pack_id+"</b> успешно поддтверждена.";
            modal.data.reload = 1;
        }
        modal.message = msg;
        modal.hideLoader();
        modal.applyParams();
    })
}
function NoBtn(event) {
    var modal = event.data;
    modal.close();
    if (modal.data.reload){
        location.reload(true);
    }
}
$(document).ready(function (event) {
    GetCertTable();
});
