$(document).ready(function(){
     
    // for not autocomplete browser
    $("input.autocomplete-off").attr("autocomplete", "off");
    
    /* Tables
================================================== */
    //===== Check all checbboxes =====//
	
    $(".titleIcon input:checkbox").click(function() {
            var checkedStatus = this.checked;
            $("#checkAll tbody tr td:first-child input:checkbox").each(function() {
                    this.checked = checkedStatus;
                            if (checkedStatus == this.checked) {
                                    $(this).closest('.checker > span').removeClass('checked');
                            }
                            if (this.checked) {
                                    $(this).closest('.checker > span').addClass('checked');
                            }
            });
    });	

    $('#checkAll tbody tr td:first-child').next('td').css('border-left-color', '#CBCBCB');
    //===== Check one checbbox =====//
    $(".checker input:checkbox").live("click",function() {
            var checkedStatus = this.checked;
            if (checkedStatus == this.checked) {
               $(this).closest('.checker > span').removeClass('checked');
            }
            if (this.checked) {
                    $(this).closest('.checker > span').addClass('checked');
            }
    });	
    //===== Datepickers =====//
	
    $("#edit_date_of_birth").datepicker({
        autoSize: true,
        dateFormat: 'yy-mm-dd'
    });
   
   
    // Событие для клика по иконке EDIT
    $(".edit_field").live("click",function(){
        
        // Меняем контент модального окна
        //$("#content-dialog-modal").html('<form><div class="clear"><label for="edit_name">Edit Name</label><input type="text" name="edit_name" id="edit_name" class="text ui-widget-content ui-corner-all autocomplete-off" /></div><div class="clear"><label for="edit_date_of_birth">Edit Date of Birth</label><input type="text" name="edit_date_of_birth" id="edit_date_of_birth" class="text ui-widget-content ui-corner-all autocomplete-off datepicker" /></div><div class="clear"><label for="edit_phone">Edit Phone</label><input type="text" name="edit_phone" id="edit_phone" class="text ui-widget-content ui-corner-all autocomplete-off" /></div><div class="clear"><label for="edit_email">Edit Email</label><input type="text" name="edit_email" id="edit_email" class="text ui-widget-content ui-corner-all autocomplete-off" /></div></form>');
        //$("#content-dialog-modal").css("padding-top","0px");
        
        // Получаем значение аттрибуда - ID данного поля в БД
        var fieldId = $(this).attr("id");
        
        // Получаем с помощью AJAX слово для редактирования
        $.ajax({
            type: "POST",
            dataType: "json",
            async: false,
            url: gBaseUrl + "lists/getfield/"+fieldId,
            data: {
                status:"get"
            },
            success: function(data){
                // При успешном AJAX
                switch(data.result)
                {
                    case "error":
                        alert(data.data);
                        break;
                    case "complete":
                        // Вывод текущего значения поля из БД
                        $("#edit_name").val(data.data.name);
                        $("#edit_date_of_birth").val(data.data.date_of_birth);
                        $("#edit_phone").val(data.data.phone);
                        $("#edit_email").val(data.data.email);
                        break;
                    default:
                        alert("Undefined error");
                        break;
                } 
            }
        });

        // Показываем диалоговое окно с редактируемыми полями
        $("#dialog-modal" ).dialog({
            title: "Edit Field",
            height: 290,
            width: 460,
            modal: true,
            hide: "explode",
            buttons: {
                "Update Field": function() {
                    // Если пользователь нажал обновить
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        async: false,
                        url: gBaseUrl + "lists/updatefield/",
                        data: {
                            name:$("#edit_name").val(),
                            date_of_birth:$("#edit_date_of_birth").val(),
                            phone:$("#edit_phone").val(),
                            email:$("#edit_email").val(),
                            fieldId:fieldId,
                            current_page:gCurrentPage
                        },
                        success: function(data){
                            // Если AJAX успешен
                            switch(data.result)
                            {
                                case "error":
                                    alert(data.data);
                                    break;
                                case "complete":
                                    // Делаем перерисовку нашей таблицы
                                    $("#checkAll tbody").html(data.data);
                                     // Перерисовываем наш пэйджинатор
                                    $("#top_pagination").html(data.pagination);
                                    break;
                                default:
                                    alert("Undefined error");
                                    break;
                            }
                        }
                    });
                    // И сразу закрываем мождальное окно
                    $(this).dialog( "close" );
                    
                    return false;                
                },
                Cancel: function() {
                    // Кнопка отмены 
                    $(this).dialog("close");
                }
        }
        });
    });
    
    // Событие для клика по иконке DELETE
    $(".delete_field").live("click",function(){
        
        // Получаем значение аттрибуда - ID данного поля в БД
        var fieldId = $(this).attr("id");
        // Меняем контент модального окна
        $("#content-dialog-modal").html("Are you sure you want to remove this field?");
        $("#content-dialog-modal").css("padding-top","20px");
        
        // Показываем диалоговое окно с подверждением удаления
        $("#dialog-modal" ).dialog({
            title: "Delete Field",
            height: 160,
            width: 260,
            modal: true,
            hide: "explode",
            buttons: {
                "Delete Field": function() {
                    // Если пользователь нажал удалить
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        async: false,
                        url: gBaseUrl + "lists/deletefield/",
                        data: {
                            fieldId:fieldId,
                            current_page:gCurrentPage
                        },
                        success: function(data){
                            // Если AJAX успешен
                            switch(data.result)
                            {
                                case "error":
                                    alert(data.data);
                                    break;
                                case "complete":
                                    // Делаем перерисовку нашей таблицы
                                    $("#checkAll tbody").html(data.data);
                                     // Перерисовываем наш пэйджинатор
                                    $("#top_pagination").html(data.pagination);
                                    break;
                                default:
                                    alert("Undefined error");
                                    break;
                            }
                        }
                    });
                    // И сразу закрываем мождальное окно
                    $(this).dialog( "close" );
                    
                    return false;                
                },
                Cancel: function() {
                    // Кнопка отмены 
                    $(this).dialog("close");
                }
        }
        });
    });
    
    // For pagination
    $("div.pagination ul.pages li a").live("click",function(){

        gCurrentPage = $(this).attr("page");
        
        $.ajax({
            type: "POST",
            dataType: "json",
            async: false,
            url: gBaseUrl+ "lists/pagination",
            data: {
                current_page:gCurrentPage
            },
            success: function(data){
                // Если AJAX успешен
                switch(data.result)
                {
                    case "error":
                        alert(data.data);
                        break;
                    case "complete":
                        // Делаем перерисовку нашей таблицы
                        $("#checkAll tbody").html(data.data);
                        // Перерисовываем наш пэйджинатор
                        $("#top_pagination").html(data.pagination);
                        break;
                    default:
                        alert("Undefined error");
                        break;
                }
            }
        });
    });
    
    
});