/**
 * Created by Nef1k on 18.09.2016.
 *
 * Requires CertificateList
 */

var certificateToAttach = [];
var isAJAXing = false;
var attachModalSelector = "#attach-certificate-modal";

function parseAttachCertificateList(){
    var ID_Certificate = parseInt($("[name='attach_ID']").val());
    var range = {
        from: parseInt($("[name='attach_range_from']").val()),
        to: parseInt($("[name='attach_range_to']").val())
    };
    var amount = {
        from: parseInt($("[name='attach_amount_from']").val()),
        count: parseInt($("[name='attach_amount_count']").val())
    };

    if (!isInvalid(ID_Certificate)){
        return {
            type: "single",
            certificates: [ID_Certificate]
        };
    }

    if (!isInvalid(range.from) && !isInvalid(range.to)){
        return {
            "type": "range",
            "certificates": generateRange(range.from, range.to)
        }
    }

    if (!isInvalid(amount.from) && !isInvalid(amount.count)){
        return {
            "type": "amount",
            "certificates": generateAmount(amount.from, amount.count)
        };
    }
}

function getCertDOMID(cert_id){
    return "#unattached-cert-"+cert_id;
}
function isCertMarked(cert_id){
    return $(getCertDOMID(cert_id)).hasClass("btn-info");
}
function markCert(cert_id){
    //console.log("marked cert #" + cert_id);
    $(getCertDOMID(cert_id)).addClass("btn-info");
    $(getCertDOMID(cert_id)).removeClass("btn-default");

    certificateToAttach.push(cert_id);
}
function unmarkCert(cert_id){
    //console.log("unmarked cert #" + cert_id);
    $(getCertDOMID(cert_id)).removeClass("btn-info");
    $(getCertDOMID(cert_id)).addClass("btn-default");

    var cert_index = certificateToAttach.indexOf(cert_id);
    certificateToAttach.splice(cert_index, 1);
}
function toggleCert(cert_id){
    if (isCertMarked(cert_id)){
        unmarkCert(cert_id);
    }
    else{
        markCert(cert_id);
    }
}
function toggleCertList(cert_ids){
    cert_ids.forEach(function(cert_id, i){
        toggleCert(cert_id);
    });
}

function attachBtnClick(event){
    var user_id = $(this).data("user_id");
    var username = $(this).data("username");

    $(attachModalSelector).data("user_id", user_id);
    $(attachModalSelector).data("username", username);
}
function onHelperAddBtnClick(event){
    var certificates = parseAttachCertificateList().certificates;
    toggleCertList(certificates);
}
function onCertBtnClick(event){
    var cert_id = $(this).data("cert-id");
    toggleCert(cert_id);
}
function onSaveBtnClick(event){
    var user_id = $(attachModalSelector).data("user_id");

    if (!isAJAXing){
        isAJAXing = true;
    }
    certificateToAttach.forEach(function(cert_id, i){
        var postParams = {
            id: cert_id,
            field_names: JSON.stringify(["user_id", "id_cert_state"]),
            field_values: JSON.stringify([user_id, 1])
        };

        jQuery.post("/certificate/edit", postParams, function(data){
            console.log(data);
        });
    });
}

function onModalLoad(event){
    var user_id = $(this).data("user_id");
    var username = $(this).data("username");

    //Clearing up previous
    $(".attached-certificates-empty").addClass("hidden");
    $(".unattached-certificates-empty").addClass("hidden");
    $(".certificate-row").remove();
    $(".unatt_btn").remove();

    var attached_loader = $(".attached-certificates-loader").removeClass("hidden");
    var unattached_loader = $(".unattached-certificates-loader").removeClass("hidden");
    $("#username").html(username);

    //Sending AJAX request to retrieve attached certificates
    jQuery.getJSON("/admin/attach?user_id="+user_id, fillAttachModalWithData);
}

function fillUserTableWithData(table_selector, data){
    $("#unattached_certs_count").html(data.unattached_certs);
    data.users.forEach(function(item, i){
        $(table_selector).after(
            "<tr>" +
            "<td>" + item.ID_User + "</td>" +
            "<td><a href='"+ item.userInfoLink +"'>" + item.username + "</a></td>" +
            "<td>" +
            "<button data-toggle='modal' data-target='#attach-certificate-modal' class=\"btn btn-xs btn-default attach-certificate-btn\" data-user_id=\""+ item.ID_User +"\" data-username=\""+item.username+"\">" +
            "<span class='glyphicon glyphicon-link'></span>" +
            "</button>" +
            "</td>" +
            "<td>" + item.role + "</td>" +
            "<td><a href=\"mailto:"+ item.email +"\">" + item.email + "</a></td>" +
            "</tr>"
        );
    });
}
function fillAttachModalWithData(data){
    var attached_loader = $(".attached-certificates-loader").addClass("hidden");
    var unattached_loader = $(".unattached-certificates-loader").addClass("hidden");
    if (data.att_certs.length == 0){
        $(".attached-certificates-empty").removeClass("hidden");
    }
    else {
        data.att_certs.forEach(function(item, i){
            $("#certificates-table tr:last").after(
                "<tr class='certificate-row'>" +
                "   <td>" + item.ID_certificate + "</td>" +
                "   <td>" + item.CertState + "</td>" +
                "</tr>"
            );
        });
    }
    if (data.unatt_certs.length == 0){
        $(".unattached-certificates-empty").removeClass("hidden");
    }
    else {
        data.unatt_certs.forEach(function (item, i) {
            $("#unattached_certs h4").after(
                "<button class=\"unatt_btn btn btn-default col-md-3\" data-cert-id='" + item.ID_certificate + "' id=\"unattached-cert-" + item.ID_certificate + "\">" + item.ID_certificate + "</button>"
            );
        });

        $(".unatt_btn").click(onCertBtnClick);
    }
}

$(document).ready(function(event){
    $(attachModalSelector).on("show.bs.modal", onModalLoad);
    $("#attach_helper_add_btn").click(onHelperAddBtnClick);
    $("#save-btn").click(onSaveBtnClick);
});