/**
 * Created by 166878 on 15.11.2016.
 */

function GetCertPacks() {
    $(".user-packs").remove();
    $(".pack_loader").removeClass("hidden");
    var field_names = JSON.stringify(["ID_Sertificate","cert_link", "name", "last_name", "phone_number", "flight_type", "price"]);
    jQuery.post("/certificate_pack/select", {field_names:field_names}, function (data) {
        $(".pack_loader").addClass("hidden");
        console.log(data);
        var user = "";
        var user_price = 0;
        var pack_price = 0;
        var percent = 0;
        if (data.length == 0){
            $("#packs").append(
                "<div class='text-center user-packs'><h3 class='user-packs'>Нет пакетов сертификатов в системе</h3></div>"
            )
        }
        else {
            data.forEach(function (item) {
                pack_price = 0;
                if (item.user_login != user) {
                    user = item.user_login;
                    percent = parseInt(item.percent);
                    if (isNaN(percent) || (percent < 0) || (percent > 100)) {
                        percent = 0
                    }
                    user_price = 0;
                    $("#packs").append(
                        "<div class='panel panel-default user-packs' >" +
                        "<div class='panel-heading'><strong>" + user + " (процент от продаж: " + percent + "%)</strong>" +
                        "<div class='pull-right'>" +
                        "<button class='btn btn-xs btn-default mark_all' data-user='" + user + "' onclick='mark_all(this)'>Выделить всё</button>" +
                        "<button class='btn btn-xs btn-default unmark_all' data-user='" + user + "' onclick='unmark_all(this)'>Снять выделение</button>" +
                        "</div>" +
                        "</div>" +
                        "<div class='panel-body'>" +
                        "<div class='panel panel-default'>" +
                        "<div class='panel-heading'>" +
                        "<input style='cursor: pointer' type='checkbox' autocomplete='off' class='packs_of_" + user + "' data-pack_id='" + item.pack_id + "'>" +
                        "<span style='cursor: pointer' class='for_click'  data-toggle='collapse' data-target='#pack_" + item.pack_id + "'>" +
                        "<b>  Пакет №" + item.pack_id + " <span class='glyphicon glyphicon-chevron-down'></span> (способ оплаты: "+item.pack_payment_method+")</b>" +
                        "</span> " +
                        "<b><div class='pull-right'>Сумма: <span id='pack_price_" + item.pack_id + "'>0</span> р.</div></b>" +
                        "</div>" +
                        "<table class='table table-hover table-striped user_pack collapse' id='pack_" + item.pack_id + "'>" +
                        "<tr>" +
                        "<th class='col-md-1 text-center' style='vertical-align: middle'>ID сертификата</th>" +
                        "<th class='col-md-3 text-center' style='vertical-align: middle'>Клиент</th>" +
                        "<th class='col-md-2 text-center' style='vertical-align: middle'>Телефон</th>" +
                        "<th class='col-md-1 text-center' style='vertical-align: middle'>Цена</th> " +
                        "<th class='col-md-2 text-center' style='vertical-align: middle'>Цена с учетом %</th> " +
                        "<th class='col-md-3 text-center' style='vertical-align: middle'>Тип полёта</th>" +
                        "</tr>" +
                        "</table>" +
                        "</div>" +
                        "</div>" +
                        "<div class='panel-heading'><b>Общая сумма:<span class='pull-right'><span id='user_price_" + user + "'></span> p.</span></b></div> " +
                        "</div>"
                    );
                }
                else {
                    $(".user-packs:last-child .panel-body").append(
                        "<div class='panel panel-default'>" +
                        "<div class='panel-heading'>" +
                        "<input style='cursor: pointer' type='checkbox' autocomplete='off' class='packs_of_" + user + "' data-pack_id='" + item.pack_id + "'>" +
                        "<span style='cursor: pointer' class='for_click'  data-toggle='collapse' data-target='#pack_" + item.pack_id + "'>" +
                        "<b>  Пакет №" + item.pack_id + " <span class='glyphicon glyphicon-chevron-down'></span>  (способ оплаты: "+item.pack_payment_method+")</b>" +
                        "</span> " +
                        "<b><div class='pull-right'>Сумма: <span id='pack_price_" + item.pack_id + "'>0</span> р.</div></b>" +
                        "</div>" +
                        "<table class='table table-hover table-striped user_pack collapse' id='pack_" + item.pack_id + "'>" +
                        "<tr style='width:100%'>" +
                        "<th class='col-md-1 text-center' style='vertical-align: middle'>ID сертификата</th>" +
                        "<th class='col-md-3 text-center' style='vertical-align: middle'>Клиент</th>" +
                        "<th class='col-md-2 text-center' style='vertical-align: middle'>Телефон</th>" +
                        "<th class='col-md-1 text-center' style='vertical-align: middle'>Цена</th> " +
                        "<th class='col-md-2 text-center' style='vertical-align: middle'>Цена с учетом %</th> " +
                        "<th class='col-md-3 text-center' style='vertical-align: middle'>Тип полёта</th>" +
                        "</tr>" +
                        "</table>" +
                        "</div>"
                    )
                }
                item.certificates.forEach(function (cert) {
                    var name = "", last_name = "", phone_number = "";
                    var flight_type = "", price_perc = 0, price = 0;
                    if (cert.name) {
                        name = cert.name;
                    }
                    if (cert.last_name) {
                        last_name = cert.last_name;
                    }
                    if (cert.phone_number) {
                        phone_number = cert.phone_number;
                    }
                    if (cert.flight_type) {
                        flight_type = cert.flight_type;
                    }
                    if (cert.price) {
                        price_perc = parseInt(cert.price) * (1 - percent / 100);
                        price = cert.price;
                    }
                    pack_price += price_perc;
                    user_price += price_perc;
                    $("#pack_" + item.pack_id).append(
                        "<tr class='certificate_row' style='width:100%'>" +
                        "<th class='text-center'><a href='" + cert.cert_link + "' target='_blank'>" + cert.ID_Sertificate + "</a></th>" +
                        "<td class='text-center'>" + name + " " + last_name + "</td>" +
                        "<td class='text-center'>" + phone_number + "</td>" +
                        "<td class='text-center'>" + price + "</td>" +
                        "<td class='text-center'><b>" + price_perc + "</b></td>" +
                        "<td class='text-center'>" + flight_type + "</td>" +                        "</tr>"
                    )
                });
                $("#pack_price_" + item.pack_id).html(pack_price);
                $("#user_price_" + item.user_login).html(user_price);
            })
        }
    });
}

function mark_all(data) {
    var name = $(data).attr('data-user');
    $(".packs_of_"+name).prop("checked",true);
}

function unmark_all(data) {
    var name = $(data).attr('data-user');
    $(".packs_of_"+name).prop("checked",false);
}

function SendPacks() {
    var checked_packs = [];
    var selector = $("input:checked");
    var msg="";
    for (var i = 0; i<selector.length; ++i){
        checked_packs[i] = parseInt($(selector[i]).attr("data-pack_id"));
    }
    var action_id = parseInt($("#pack_action").val());
    var yesNoDialog = new YesNoDialog;
    yesNoDialog.setModalSelector("#yes-no-modal");
    yesNoDialog.show({
        caption: "Подтвердите действие",
        yes_caption: "Ок",
        no_caption: "",
        data: {checked_packs : checked_packs, action: action_id, reload: 0},
        yes_handler: NoBtn,
        no_handler: NoBtn
    });
    if(checked_packs.length == 0){
        msg = "Пакеты не выбраны";
    }
    else {
        yesNoDialog.no_caption="Отмена";
        msg = "Выбранные пакеты: " + checked_packs.join(", ")+".<br>" +
            "Выбранное действие: ";
        msg += action_id?"Активировать":"Удалить";
        yesNoDialog.yes_handler = YesBtn
    }
    yesNoDialog.message = msg;
    yesNoDialog.applyParams();
}
function YesBtn(event) {
    var modal = event.data;
    var msg ="";
    modal.no_caption="";
    modal.message="";
    modal.yes_caption = "Ок";
    modal.showLoader();
    modal.yes_handler = NoBtn;
    modal.applyParams();
    var packs = JSON.stringify(modal.data.checked_packs);
    var action = modal.data.action;
    jQuery.post("/certificate_pack/action", {pack_id : packs, is_activated: action}, function (data) {
        console.log(data);
        var errors = data.error_msg;
        if (errors.length > 0){
            msg = "Возникли следующие ошибки при выполнении действия: <br>" + errors.join(" <br>")+".";
        }
        else{
            msg = "Действие успешно выполнено.";
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
        GetCertPacks();
    }
}
$(document).ready(function (event) {
    GetCertPacks();
});