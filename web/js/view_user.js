function GetUserFiles(){
    var user_id=$("#user_id").val();
    console.log(user_id);
    var field_names = JSON.stringify(["display_name", "file_type", "file_name", "ID_File", "file_link"]);
    var criteria = JSON.stringify({ "ID_User": [user_id] });
    var sort = JSON.stringify({});
    var watcher_group = parseInt($("#user_files_table").attr("data-group"));
    jQuery.post("/files/select",{field_names: field_names, criteria: criteria, sort: sort}, function (data) {
        console.log(data);
        $(".loader").addClass("hidden");
        if (data.length == 0){
            $("#user_files_table").append("<tr class='file-row'><th colspan='3'>У данного пользователя нет файлов</th></tr>")
        }
        else{
            $("#user_files_table").append(
                "<tr  class='file-row'>" +
                    "<th></th>" +
                    "<th class='col-md-4 text-left'>Файл </th>" +
                    "<th class='col-md-8 text-left'>Описание </th>" +
                "</tr>"
            );
            data.forEach(function(item){
                $("#user_files_table").append(
                    "<tr class='file-row'>" +
                    "<td class='file-checkbox'></td>"+
                    "<td class='col-md-4 text-left'><a href='"+ item.file_link +"'>" + item.file_name + "</a></td> " +
                    "<td class='col-md-8 text-left'>" + item.display_name + "</td>" +
                "</tr>")
                if (watcher_group ==3){
                        $("#user_files_table tr:last-child .file-checkbox").html(
                            "<input style='cursor:pointer;' type='checkbox' autocomplete='off' class='file' data-file_id='"+item.ID_File+"'>"
                        )
                }

            });
            if (watcher_group == 3) {
                $("#user_files_table").after(
                    "<div class='text-right file-row'><button class='btn btn-danger' onclick='DeleteFiles()'><span class='glyphicon glyphicon-trash'></span>&nbsp;&nbsp;Удалить файлы</button></div>"
                );
                $("#user_files_table").before(
                    "<div class='pull-right file-row'>" +
                    "<button class='btn btn-xs btn-default mark_all' onclick='mark_all()'>Выделить всё</button>" +
                    "<button class='btn btn-xs btn-default unmark_all' onclick='unmark_all()'>Снять выделение</button>" +
                    "</div>"
                )
            }
        }
    })
}
function mark_all() {
    $(".file").prop("checked",true);
}

function unmark_all() {
    $(".file").prop("checked",false);
}

function DeleteFiles() {
    var checked_files = [];
    var selector = $("input[type='checkbox']:checked");
    for (var i = 0; i<selector.length; ++i){
        checked_files[i] = parseInt($(selector[i]).attr("data-file_id"));
    }
    console.log(checked_files);
    var yesNoDialog = new YesNoDialog;
    yesNoDialog.setModalSelector("#yes-no-modal");
    yesNoDialog.show({
        caption: "Удаление файлов",
        yes_caption: "Ок",
        no_caption: "",
        yes_handler: YesBtn
    });
    yesNoDialog.showLoader();
    var msg = "Файлы успешно удалены";
    if (checked_files.length == 0){
        msg="Файлы не выбраны";
        yesNoDialog.message = msg;
        yesNoDialog.hideLoader();
        yesNoDialog.applyParams();
    }
    else {
        var file_ids = JSON.stringify(checked_files);
        jQuery.post("/files/file_delete", {file_ids : file_ids}, function (data) {
            console.log(data);
            var errors = data.error_msg;
            if (errors.length > 0){
                msg = "Возникли следующие ошибки при удалении файлов: <br>" + errors.join(" <br>")+".";
            }
            else {
                $(".file-row").remove();
                $(".loader").removeClass("hidden");
                GetUserFiles();
            }
            yesNoDialog.message = msg;
            yesNoDialog.hideLoader();
            yesNoDialog.applyParams();
        })
    }

}

function YesBtn(event) {
    var modal = event.data;
    modal.close();
}

$(document).ready(function (event) {
    GetUserFiles();
});