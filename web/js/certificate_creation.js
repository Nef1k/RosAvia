/**
 * Created by ASUS on 20.09.2016.
 */

function isDefined(varible){
    return (typeof varible !== "undefined");
}

function createModalNoBtn(event){
    /** @var YesNoDialog modal */
    var modal = event.data;
    modal.close();
    jQuery.getJSON("/admin/user_table", function (data){
        $("#unattached_certs_count").html(data.unattached_certs)
    })
}

function createModalYesBtn(event) {
    /** @var YesNoDialog modal */
    var modal = event.data;

    if (modal.data !== "undefined") {
        modal.showLoader();
        console.log("Creating certificates:", modal.data);
        jQuery.post("/admin/cert_creation", {cert_ids: JSON.stringify(modal.data)}, function(data){
            modal.hideLoader();
            modal.no_caption = "";
            modal.yes_caption = "Ок";
            modal.yes_handler = createModalNoBtn;



            var errors = data.error_msg;
            if (errors.length > 0){
                modal.message = "Возникли следующие ошибки при добавлении сертификатов: <br>" + errors.join(" <br>");
                $("#create_certs_hided_menu input").val("");
            }
            else{
                modal.message = "Сертификаты <code>"+ modal.data.join(", ") +"</code> успешно добавлены!";
                jQuery.getJSON("/admin/user_table", function (data){
                    $("#unattached_certs_count").html("");
                    $("#unattached_certs_count").html(data.unattached_certs);
                });
                $("#create_certs_hided_menu input").val("");
                modal.yes_attribute1_name = "data-toggle";
                modal.yes_attribute1_value = "collapse";
                modal.yes_attribute2_name = "data-target";
                modal.yes_attribute2_value = "#create_certs_hided_menu";
            }
            modal.applyParams();
            $("input").val("");        });

    }
}

function isInvalid(value){
    return isNaN(value) || value == "";
}

function generateRange(from, to){
    var result = [];
    for (var i = from; i <= to; i++){
        result.push(i);
    }

    return result;
}
function generateAmount(offset, count){
    var result = [];

    for (var i = offset; i <= offset + count - 1; i++){
        result.push(i);
    }

    return result;
}

function parseCreateCertificateList(){
    var ID_Certificate = parseInt($("[name='ID_Certificate']").val());
    var range = {
        from: parseInt($("[name='range_from']").val()),
        to: parseInt($("[name='range_to']").val())
    };
    var amount = {
        from: parseInt($("[name='amount_from']").val()),
        count: parseInt($("[name='amount_count']").val())
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

function createBtnClick(){
    var userInput = parseCreateCertificateList();
    var certificateListStr = (userInput) ? userInput.certificates.join(", ") : "undefined";

    var msg = (userInput) ?
        "Будут созданы сертификаты со следующими ID: <code>"+ certificateListStr +"</code>. Вы уверены?" :
        "Неправильно заполнены поля";

    var yesNoDialog = new YesNoDialog;
    yesNoDialog.setModalSelector("#yes-no-modal");
    yesNoDialog.hideLoader();
    yesNoDialog.show({
        caption: "Создание сертификатов",
        message: msg,

        yes_caption: "Да",
        no_caption: "Нет",

        data: (userInput) ? userInput.certificates : "undefined",

        yes_handler: createModalYesBtn,
        no_handler: createModalNoBtn
    });
}