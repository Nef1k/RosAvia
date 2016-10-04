/**
 * Created by 166878 on 20.09.2016.
 */
function certificateToStr(certificate){
    return  "<a href='#' class='btn btn-primary' style='margin-bottom: 5px;'>" +
            "   <span class='badge'>" + certificate.id + "</span>" +
            "   " + certificate.flight_type +
            "</a> ";
}
function getHourRow(hour){
    return  "<tr class='hour-row'>" +
            "   <td valign='middle' align='center'>" + hour + ":00</td>" +
            "   <td style='padding: 10px 20px;'>" +
            "       <div class='row certificates-in-hour-" + hour + "'></div>" +
            "   </td>" +
            "</tr>";
}

function showModal(date){
    //Showing modal window with per-day schedule table
    $("#chosen_date").html(date.format("DD.MM.YYYY"));
    $(".current-date").html(date.format("YYYY-MM-DD"));
    $("#time_table").find(".hour-row").remove();
    $("#found_certs").find(".cert_row").remove();
    $(".day-empty").addClass("hidden");
    $("#myModal").modal("show");

    //Retrieving per-day schedule
    var chosen_date = date.unix();
    getTimeTableData(chosen_date);
    findCertificates();
}
function fillTimeTableWithData(table_selector, data){
    //If there is more than one busy hour
    if (Object.keys(data).length > 0) {

        //Iterating over the hours
        for (var hour in data) {
            //Retrieve next hour
            if (!data.hasOwnProperty(hour)) {
                continue;
            }
            var certificates_in_hour = data[hour];

            //If there is more than one flight in that hour
            if (certificates_in_hour.length != 0) {
                //Creating new hour row in table
                $(table_selector).append(getHourRow(hour));

                //Iterating over the flights in this hour and rendering them
                certificates_in_hour.forEach(function(item, i, arr){
                    $(table_selector + " .certificates-in-hour-"+hour).append(certificateToStr(item));
                });
            }
        }
    }
    //Otherwise showing the string telling the day is empty
    else {
        $(table_selector + " .day-empty").removeClass("hidden");
    }
}
function getTimeTableData(date){
    $("#time_table_loader").removeClass("hidden");
    jQuery.getJSON("/admin/show_day_schedule?date="+date, function (data){
        $("#time_table_loader").addClass("hidden");
        fillTimeTableWithData("#time_table", data);
    });
}

function getCertRow(cert) {
    return "<tr class='cert_row'>" +
        "   <td>" +
        "       <a href='" + cert.cert_link + "'>"+cert.ID_Sertificate+"</a>" +
        "   </td>" +
        "   <td>"+
                cert.name + " " + cert.last_name + "" +
        "   </td>" +
        "   <td>" +
        "       <button class='btn btn-xs btn-default' data-toggle='collapse' data-target='#" + cert.ID_Sertificate + "'>" +
        "           <span class='glyphicon glyphicon-triangle-bottom'></span>" +
        "       </button>" +
        "   </td>" +
        "   </tr>" +
        "   <tr id='"+cert.ID_Sertificate+"' class='collapse cert_row'>" +
        "    <td colspan='3'>" +
        "           <div class='input-group'>" +
        "               <input type='text' class='form-control' placeholder='Часы' id='hours-"+cert.ID_Sertificate+"'>" +
        "               <span class='input-group-addon'><b>:</b></span>" +
        "               <input type='text' class='form-control' placeholder='Минуты' id='minutes-"+cert.ID_Sertificate+"'>" +
        "               <span class='input-group-btn'>"+
        "                   <button class='btn btn-default set-use-time-btn' type='button' data-certificate-id='"+ cert.ID_Sertificate +"'>Ok</button>" +
        "               </span>" +
        "           </div>" +
        "   </td>" +
        "   </tr>"
}

function onYesSetTime(event){
    var modal_window = event.data;
    var date = moment(modal_window.data.date).unix();
    var certificate_id = JSON.stringify(modal_window.data.certificate_id);
    var fieldnames = JSON.stringify(["use_time"]);
    var fieldvalues = JSON.stringify([date]);
    modal_window.showLoader();
    //TODO: Make post query to set use_time to certificate
    jQuery.post("/certificate/edit", {id : certificate_id, field_names : fieldnames, field_values: fieldvalues}, function (data) {
        console.log(data)
    });
    modal_window.hideLoader();
    modal_window.close();
    $("#time_table").find(".hour-row").remove();
    $("#found_certs").find(".cert_row").remove();
    getTimeTableData(date);
    findCertificates();
}

function onNoSetTime(event){

}

function onSetUseTime(event){
    var btn = $(this);

    var certificate_id = $(this).data("certificate-id");
    if (certificate_id == "undefined"){
        return false;
    }

    var hours = $("#hours-"+certificate_id).val();
    var minutes = $("#minutes-"+certificate_id).val();
    var seconds = "00";
    var date=$(".current-date").html() + " " + [hours, minutes, seconds].join(":");
    //ToDo check validate time
    var dialog = new YesNoDialog();
    dialog.setModalSelector("#yes-no-modal");
    dialog.show({
        caption: "Установка времеи",
        message: date,

        yes_caption: "Установить",
        no_caption: "Отмена",

        data: {
            "certificate_id": certificate_id,
            "date": date
        },

        yes_handler: onYesSetTime,
        no_handler: onNoSetTime
    });

    return true;
}

function fillCertsListWithData(table_selector,data){
    data.forEach(function(item) {
       $(table_selector).append(getCertRow(item))
    });
    $(".set-use-time-btn").click(onSetUseTime);
    $("#find_cert").click(data, findByIDCertificate);
    $("#clear_filter").click(data, clearSearchFilter);
}

function findCertificates() {
    $("#found_cert_loader").removeClass("hidden");
    var fields = JSON.stringify(["ID_Sertificate", "flight_type", "cert_link", "name", "last_name"]);
    var criteria = JSON.stringify({ "ID_SertState": [5]});
    jQuery.post("/certificate/select", {field_names : fields, criteria : criteria}, function (data) {
        $("#found_cert_loader").addClass("hidden");
        fillCertsListWithData("#found_certs",data)
    })

}

function findByIDCertificate(data){
    var id = $("#cert_id_for_watching").val();
    var found = false;
    var certificate_list = data.data;

    certificate_list.forEach(function (item) {
        if (item.ID_Sertificate == id){
            $("#found_certs").find(".cert_row").remove();
            $("#found_certs").append(getCertRow(item));
            found = true;
        }
    });
    if (!found){
        $("#found_certs").find(".cert_row").remove();
        $("#found_certs").append("<tr class='cert_row'><td colspan='3'>Сертификат не найдет</td></tr>");
    }
}

function clearSearchFilter(event){
    var certificate_list = event.data;
    $("#cert_id_for_watching").val("");
    $("#found_certs").find(".cert_row").remove();

    certificate_list.forEach(function (item) {
        $("#found_certs").append(getCertRow(item));
    });
}

$(document).ajaxError(function(event, jqXHR, ajaxSettings, message){
    console.log(message);
});

$(document).ready(function(e){
    $("#some-btn").click(showModal);

    $("#calendar").fullCalendar({
        monthNames: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
        header: {
            left: 'title',
            right: 'today, prev, next'
        },
        dayClick: showModal
    });
});