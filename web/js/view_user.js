function GetUserFiles(){
    var user_id=$("#user_id").val();
    console.log(user_id);
    var field_names = JSON.stringify(["display_name", "file_type", "file_name"]);
    var criteria = JSON.stringify({ "ID_User": user_id });
    var sort = JSON.stringify({});
    jQuery.post("/files/select",{field_names: field_names, criteria: criteria, sort: sort}, function (data) {
        console.log(data);
        $(".loader").addClass("hidden");
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