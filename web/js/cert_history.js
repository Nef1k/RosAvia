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
                        "<td>" + item.user_name + "</td>" +
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

$(document).ready(function (event) {
    getCertHistory();
});