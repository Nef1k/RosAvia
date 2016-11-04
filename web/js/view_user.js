function GetUserFiles(){
    var user_id=$("#user_files_table").attr("data-user_id");
    console.log(user_id);
}

function AddFile() {
    var user_id=$("#user_files_table").attr("data-user_id");
    var description=$("#description").val();
    var file=$("#InputFile").val();
    console.log(user_id, description, file);
    jQuery.post("/files/file_set", { user_id: user_id, display_name: description, userile: file}, function (data) {
        console.log(data);
    })
}

$(document).ready(function (event) {
    GetUserFiles();
});