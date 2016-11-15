/**
 * Created by 166878 on 15.11.2016.
 */

function GetCertPacks() {
    $("user_packs").remove();
    var field_names = JSON.stringify(["ID_Sertificate", "name", "last_name", "phone_number", "flight_type", "price"]);
    jQuery.post("/certificate_pack/select", {field_names:field_names}, function (data) {
        $(".pack_loader").addClass("hidden");
        console.log(data)
    });
}

$(document).ready(function (event) {
    GetCertPacks();
});