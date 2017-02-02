/**
 * Created by 166878 on 07.12.2016.
 */

function getCertHistory() {
    var cert_id = $("#history_table").attr("data-cert_id");
    console.log(cert_id);
    jQuery.getJSON("/admin/certificate_history_events?cert_id="+cert_id, function (data) {
        console.log(data);
        $("#loader").addClass("hidden");
        if (data.length > 0){
            data.forEach(function (item) {
                $("#history_table").append(
                    "<tr>" +
                        "<td>" + item.time.date.split('.')[0] + "</td>" +
                        "<td>" + item.action + "</td>" +
                        "<td><a href='" + item.user_link + "' >" + item.user_name + "</a></td>" +
                    "</tr>"
                )
            })
        }
        else{
            $("#history_table").append(
            "<tr><td colspan='3'>История данного сертификата пуста.</td></tr>"
            )
        }


    })
}

function useCert() {
    var id = $("#cert").data("cert_id");
    var cert_id= [id];
    var yesNoDialog = new YesNoDialog;
    console.log(cert_id);

    yesNoDialog.setModalSelector("#yes-no-modal");
    yesNoDialog.show({
        caption: "Использование сертификата",
        yes_caption: "Ок",
        no_caption: "",
        yes_handler: yesBtn
    });
    yesNoDialog.showLoader();
    var msg = "Сертификат успешно использован.";

    var postParams = {
        ids: JSON.stringify(cert_id),
        field_names: JSON.stringify(["id_cert_state"]),
        field_values: JSON.stringify(["close"])
    };

    jQuery.post("/certificate/edit", postParams, function(data){
        var errors = data.error_msg;
        if (errors.length > 0){
            msg = "Возникли следующие ошибки при добавлении сертификатов: <br>" + errors.join(" <br>");
        }
        yesNoDialog.message = msg;
        yesNoDialog.hideLoader();
        yesNoDialog.applyParams();
    })
}

function yesBtn(event) {
    var modal = event.data;
    modal.close();
    location.reload();
}

$(document).ready(function (event) {
    getCertHistory();
});