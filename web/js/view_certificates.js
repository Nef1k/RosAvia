/**
 * Created by 166878 on 20.10.2016.
 */
function parse_url(href) {
    var match = href.match(/^(https?\:)\/\/(([^:\/?#]*)(?:\:([0-9]+))?)(\/[^?#]*)(\?[^#]*|)(#.*|)$/);
    return match && {
            protocol: match[1],
            host: match[2],
            hostname: match[3],
            port: match[4],
            pathname: match[5],
            search: match[6],
            hash: match[7]
        }
}
function get_cert_status() {
    var url = parse_url(window.location.href);
    var cert_status = parseInt(url.pathname.replace("/admin/view_certificates/",""));
    return cert_status;
}
function get_certificates() {
    var fields = JSON.stringify(["ID_Sertificate","user_id", "user_login", "name", "last_name", "phone_number", "flight_type"]);
    var cert_status = get_cert_status();
    var criteria = JSON.stringify({ "ID_SertState": [cert_status]});
    var sort = JSON.stringify({"ID_User":["ASC"], "ID_Sertificate":["ASC"]});
    jQuery.post("/certificate/select", {field_names : fields, criteria : criteria, sort : sort}, function (data) {
        $(".cert_loader").addClass("hidden");
        if (data.length != 0){
            fill_cert_table_with_data("#cert_list",data);
        }
        else {
            $("#cert_list").append(
                "<div class='text-center'>" +
                    "<h3>Таких сертификатов не существует</h3>" +
                "</div>"
            )
        }

    });

}
function fill_cert_table_with_data(list_selector, data) {
    var user = "";
    data.forEach(function (item,i) {
        var name = "", last_name = "", phone_number = "";
        var flight_type = "";
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
        if (item.user_login != user){
            user = item.user_login;
            $(list_selector).append(
                "<div class='panel panel-default'>" +
                    "<div class='panel-heading'><strong>"+ user +"</strong>"+
                    "<div class='pull-right'>"+
                        "<button class='btn btn-xs btn-default mark_all' data-user='"+user+"' onclick='mark_all(this)'>Выделить всё</button>" +
                        "<button class='btn btn-xs btn-default unmark_all' data-user='"+user+"' onclick='unmark_all(this)'>Снять выделение</button>" +
                    "</div>" +
                "</div>" +
                "<table class='table table-hover table-striped'>" +
                    "<tr>" +
                        "<th class=''></th>" +
                        "<th class='col-md-1'>ID</th>" +
                        "<th class='col-md-5'>Клиент</th>" +
                        "<th class='col-md-3'>Телефон</th>" +
                        "<th class='col-md-3'>Тип полёта</th>" +
                    "</tr>" +
                    "<tr class='certificate_row' data-id='' style='cursor:pointer;' onclick='mark(this)'>" +
                        "<td><input type='checkbox' autocomplete='off' class='certs_of_"+user+"' data-cert_id='"+item.ID_Sertificate+"'></td>"+
                        "<th>"+item.ID_Sertificate+"</th>" +
                        "<td>" + name + " " + last_name + "</td>" +
                        "<td>" + phone_number + "</td>" +
                        "<td>" + flight_type + "</td>" +
                    "</tr>" +
                "</table>"
            )

        }
        else{
            $(list_selector + " table:last").append(
                "<tr class='certificate_row' data-id='' style='cursor:pointer;'  onclick='mark(this)'>" +
                "<td><input type='checkbox' autocomplete='off' class='certs_of_"+user+"' data-cert_id='"+item.ID_Sertificate+"'></td>"+
                "<th>"+item.ID_Sertificate+"</th>" +
                "<td>" + name + " " + last_name + "</td>" +
                "<td>" + phone_number + "</td>" +
                "<td>" + flight_type + "</td>" +
                "</tr>"
            )
        }

    });
}

function mark_all(data) {
    var name = $(data).attr('data-user');
    $(".certs_of_"+name).prop("checked",true);
}

function unmark_all(data) {
    var name = $(data).attr('data-user');
    $(".certs_of_"+name).prop("checked",false);
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

function YesBtn(event) {
    var modal = event.data;

    modal.close();
}

function ActionWithCertsBtn() {
    var checked_certs = [];
    var selector = $("input:checked");
    var action = $("#actions select").val();
    var yesNoDialog = new YesNoDialog;
    yesNoDialog.setModalSelector("#yes-no-modal");
    yesNoDialog.show({
        caption: "Выполнение действия",

        yes_caption: "Ок",
        no_caption: "",



        yes_handler: YesBtn
    });
    yesNoDialog.showLoader();
    var msg = "Действие успешно выполнено.";
    for (var i = 0; i<selector.length; ++i){
        checked_certs[i] = parseInt($(selector[i]).attr("data-cert_id"));
    }
    console.log(checked_certs);
    console.log($("#actions select").val());
    var postparams = {
        ids: JSON.stringify(checked_certs),
        field_names: JSON.stringify(["id_cert_action"]),
        field_values: JSON.stringify([action])
    };
    jQuery.post("/certificate/edit", postparams, function (data) {
        var errors = data.error_msg;
        if (errors.length > 0){
            msg = "Возникли следующие ошибки при выполнении действия: <br>" + errors.join(" <br>")+".";
        }
        yesNoDialog.message = msg;
        yesNoDialog.hideLoader();
        yesNoDialog.applyParams();
        console.log(data)
    })
}

function fillActionList(){
    var status = get_cert_status();
    jQuery.get("/admin/get_cert_action?cert_state="+status, function(data){
        console.log(data);
        data.actions.forEach(function (item) {
            console.log(item);
            $("#actions select").append(
                "<option value='"+item.id_action+"'>"+ item.name_action +"</option>"
            )
        })
    })
}

$(document).ready(function (event) {
    get_certificates();
    fillActionList();
});
