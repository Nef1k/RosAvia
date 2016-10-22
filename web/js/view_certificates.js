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
        console.log(data);
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
                        "<button class='btn btn-xs btn-default mark_all' data-user='"+user+"'>Выделить всё</button>" +
                        "<button class='btn btn-xs btn-default unmark_all' data-user='"+user+"'>Снять выделение</button>" +
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
                    "<tr class='certificate_row' data-id='' style='cursor:pointer;'>" +
                        "<td><input type='checkbox' autocomplete='off' class='certs_of_"+user+"'></td>"+
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
                "<tr class='certificate_row' data-id='' style='cursor:pointer;'>" +
                "<td><input type='checkbox' autocomplete='off' class='certs_of_"+user+"'></td>"+
                "<th>"+item.ID_Sertificate+"</th>" +
                "<td>" + name + " " + last_name + "</td>" +
                "<td>" + phone_number + "</td>" +
                "<td>" + flight_type + "</td>" +
                "</tr>"
            )
        }

    });
}

$(document).ready(function (event) {
    console.log(get_cert_status());
    get_certificates();
});

