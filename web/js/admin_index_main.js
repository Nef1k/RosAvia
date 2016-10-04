/**
 * Created by Nef1k on 23.09.2016.
 */

//Подгрузить пользователей в таблицу
//Подгрузить кол-во сертификатов

//Обработчик кнопки привязки сертификатов к пользователю

$(document).ready(function(event){
    $("#create_certs").click(createBtnClick);

    jQuery.getJSON("/admin/user_table", function (data){
        $(".loader-row").remove();
        fillUserTableWithData("#user_table tr:last", data);

        $(".attach-certificate-btn").click(attachBtnClick);
    });
});