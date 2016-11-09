function GetUserFiles(){
    var user_id=$("#user_id").val();
    console.log(user_id);
    var field_names = JSON.stringify(["display_name", "file_type", "file_name", "ID_File", "file_link"]);
    var criteria = JSON.stringify({ "ID_User": [user_id] });
    var sort = JSON.stringify({});
    jQuery.post("/files/select",{field_names: field_names, criteria: criteria, sort: sort}, function (data) {
        console.log(data);
        $(".loader").addClass("hidden");
        if (data.length == 0){
            $("#user_files_table").append("<tr><th colspan='3'>У данного пользователя нет файлов</th></tr>")
        }
        else{
            $("#user_files_table").append(
                "<tr>" +
                    "<th></th>" +
                    "<th class='col-md-3 text-center'>Файл </th>" +
                    "<th class='col-md-9 text-center'>Описание </th>" +
                "</tr>"
            );
            data.forEach(function(item){
                $("#user_files_table").append(
                    "<tr>" +
                    "<td><input type='checkbox' autocomplete='off' class='file' data-file_id='"+item.ID_File+"'></td>"+
                    "<td class='col-md-3 text-center'><a href='"+ item.file_link +"'>" + item.file_name + "</a></td> " +
                    "<td class='col-md-9 text-center'>" + item.display_name + "</td>" +
                "</tr>")
            });
            $("#user_files_table").after(
                "<div class='text-right'><button class='btn btn-danger' onclick='DeleteFiles'><span class='glyphicon glyphicon-trash'></span>&nbsp;&nbsp;Удалить файлы</button></div>"
            );
            $("#user_files_table").before(
                "<div class='pull-right'>"+
                "<button class='btn btn-xs btn-default mark_all' onclick='mark_all(this)'>Выделить всё</button>" +
                "<button class='btn btn-xs btn-default unmark_all' onclick='unmark_all(this)'>Снять выделение</button>" +
                "</div>"
            )
        }
    })
}

function AddFile() {
    var user_id=$("#user_files_table").attr("data-user_id");
    var description=$("#description").val();
    var file=$("#InputFile").val();
    console.log(user_id, description, file);
    jQuery.post("/files/file_set", { user_id: user_id, display_name: description, userfile: file}, function (data) {
        console.log(data);
    })
}

$(document).ready(function (event) {
    GetUserFiles();
});