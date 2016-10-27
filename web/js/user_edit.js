/**
 * Created by 166878 on 27.10.2016.
 */

function getParams() {
    $("#params .param").remove();
    $(".loader").removeClass("hidden");
    var userID = $("h1").attr("data-ID_user");
    console.log(userID);
    var user_group_id = $("#userGroup").val();
    jQuery.get("/admin/user_group_edit?user_id="+userID+"&user_group_id="+user_group_id, function (data) {
        $(".loader").addClass("hidden");
        console.log(data);
        if (data.params.length == 0){
            $("#params").append(
                "<p class='text-center'>У пользователя с данной ролью нет дополнительной информации</p>"
            )
        }
        else {
            data.params.forEach(function (item, i) {
                if (!item.value) {
                    item.value = ""
                }
                if (!(i % 2)) {
                    $("#params").append("<div class='row param'>");
                }

                $("#params .row:last").append(
                    "<div class='col-md-6 text-center'>" +
                    "<div class='input-group' id='+ item.id+'>" +
                    "<span class='input-group-addon' id='basic-addon3'><b>" + item.name + ":</b></span>" +
                    "<input data-id_field='" + item.id + "' type='text' class='addfields form-control' aria-label='...' value='" + item.value + "'>" +
                    "</div>" +
                    "</div>"
                );
                if (i % 2) {
                    $("#params").append("</div><br>");
                }
            });
            $("#params br:last").remove(0);
        }
    })
}

function SaveChanges() {
    var user_id = parseInt($("h1").attr("data-ID_user"));
    var mainFields ={
        username: $("#username").val(),
        email: $("#email").val(),
        is_activated: $("#status").val(),
        group_id: $("#userGroup").val(),
        mentor_id: $("#mentor").val()
    };
    var addFields =[];
    var sel = $(".addfields");
    for (var i=0; i<sel.length; ++i ){
        addFields[i] = {id: $(".addfields").eq(i).attr("data-id_field"), value: $(".addfields").eq(i).val()}
    }

    var yesNoDialog = new YesNoDialog;
    yesNoDialog.setModalSelector("#yes-no-modal");
    yesNoDialog.show({
        caption: "Изменение пользователя",
        yes_caption: "Ок",
        no_caption: "",
        yes_handler: changeModalYesBtn
    });
    yesNoDialog.showLoader();
    var msg = "Данные изменены.";

    var postParams = {
        user_id: user_id,
        general_settings: JSON.stringify(mainFields),
        additional_settings: JSON.stringify(addFields)
    };

    jQuery.post("/admin/user_insert", postParams, function (data) {
        console.log(data);
        var errors = data.error_msg;
        if (errors.length > 0){
            msg = "Возникли следующие ошибки: <br>" + errors.join(" <br>") + ". Повторите ввод";
        }
        yesNoDialog.message = msg;
        yesNoDialog.hideLoader();
        yesNoDialog.applyParams();
    })
}

function changeModalYesBtn (event) {
    var modal = event.data;
    modal.close();
    if (modal.message == "Данные изменены."){
        location.reload(true);
    }
}

$(document).ready(function (event) {
    getParams();
    // $("#userGroup").val.change(getParams());
});