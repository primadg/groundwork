$(document).ready(function(){
     
    // for not autocomplete browser
    $("input.autocomplete-off").attr("autocomplete", "off");
    
    
    
    /* ========================== Tables ============================= */
    /* ======== Items per page =================*/
    $("#tableFilter").change(function(){
        gItemsPerPage = $("#tableFilter option:selected").val();
        
        $("#uniform-tableFilter span").html(gItemsPerPage);
        
        // AJAX получаем данные с применением фильтра по количеству записей на странице
        $.ajax({
            type: "POST",
            dataType: "json",
            async: false,
            url: gBaseUrl + "lists/itemsperpage/",
            data: {
                search:gSearch,
                sort:gSortBy,
                field:gSortField,
                current_page:1,
                itemsperpage:gItemsPerPage
            },
            success: function(data){
                // При успешном AJAX
                switch(data.result)
                {
                    case "error":
                        alert(data.data);
                        break;
                    case "complete":
                        // Делаем перерисовку нашей таблицы
                        $("#checkAll tbody").html(data.data);
                        // Перерисовываем наш верхний пэйджинатор
                        $("#top_pagination").html(data.pagination);
                        // Перерисовываем наш нижний пэйджинатор
                        $("#bottom_pagination").html(data.pagination);
                        // обновляем счетчик общего количества записей в БД
                        $("#total_count_fields").html(data.firstNumber + " - " + data.lastNumber + " / " + data.totalCountInDB);
                        break;
                    default:
                        alert("Undefined error");
                        break;
                } 
            }
        });
    });
    
    //===== Search in table ======//
    $("#search_in_table").keypress(function(e){
        if(e.keyCode==13)
        {
            gSearch = $("#search_in_table").val();
            // AJAX получаем данные с применением поисковой фразы
            $.ajax({
                type: "POST",
                dataType: "json",
                async: false,
                url: gBaseUrl + "lists/search/",
                data: {
                    search:gSearch,
                    sort:gSortBy,
                    field:gSortField,
                    current_page:1,
                    itemsperpage:gItemsPerPage
                },
                success: function(data){
                    // При успешном AJAX
                    switch(data.result)
                    {
                        case "error":
                            alert(data.data);
                            break;
                        case "complete":
                            // Делаем перерисовку нашей таблицы
                            $("#checkAll tbody").html(data.data);
                            // Перерисовываем наш верхний пэйджинатор
                            $("#top_pagination").html(data.pagination);
                            // Перерисовываем наш нижний пэйджинатор
                            $("#bottom_pagination").html(data.pagination);
                            // обновляем счетчик общего количества записей в БД
                            $("#total_count_fields").html(data.firstNumber + " - " + data.lastNumber + " / " + data.totalCountInDB);
                            break;
                        default:
                            alert("Undefined error");
                            break;
                    } 
                }
            });
        }
    });
    
    //===== Sortable columns =====//   
    $(".sortCol").toggle(
        function () {
            // Ищем все елементы у которых присутствуют стрелочки
            // И убираем класы верхней и нижней стрелочки :)
            $(".sortCol").each(function() {
                $(this).removeClass("headerSortUp");
                $(this).removeClass("headerSortDown");
            });
            // Добавляем класс показа сортировки ASC
            $(this).addClass("headerSortDown");
            // Сохраняем тип сортировки в глобально переменной
            // чтобы можно было использовать при паджинации
            gSortBy = "asc";
            // Сохраняем имя поля -по которому нужно сортировать
            gSortField = $(this).attr("field");
            // AJAX получаем данные в отсортированом виде
            $.ajax({
                type: "POST",
                dataType: "json",
                async: false,
                url: gBaseUrl + "lists/sorttable/",
                data: {
                    sort:gSortBy,
                    field:gSortField,
                    current_page:gCurrentPage,
                    search:gSearch,
                    itemsperpage:gItemsPerPage
                },
                success: function(data){
                    // При успешном AJAX
                    switch(data.result)
                    {
                        case "error":
                            alert(data.data);
                            break;
                        case "complete":
                            // Делаем перерисовку нашей таблицы
                            $("#checkAll tbody").html(data.data);
                            // Перерисовываем наш верхний пэйджинатор
                            $("#top_pagination").html(data.pagination);
                            // Перерисовываем наш нижний пэйджинатор
                            $("#bottom_pagination").html(data.pagination);
                            // обновляем счетчик общего количества записей в БД
                            $("#total_count_fields").html(data.firstNumber + " - " + data.lastNumber + " / " + data.totalCountInDB);
                            break;
                        default:
                            alert("Undefined error");
                            break;
                    } 
                }
            });
            
        },
        function () {
            // Ищем все елементы у которых присутствуют стрелочки
            // И убираем класы верхней и нижней стрелочки :)
            $(".sortCol").each(function() {
                $(this).removeClass("headerSortUp");
                $(this).removeClass("headerSortDown");
            });
            // Добавляем класс показа сортировки DESC
            $(this).addClass("headerSortUp");
            // Добавляем класс показа сортировки ASC
            $(this).addClass("headerSortDown");
            // Сохраняем тип сортировки в глобально переменной
            // чтобы можно было использовать при паджинации
            gSortBy = "desc";
            // Сохраняем имя поля -по которому нужно сортировать
            gSortField = $(this).attr("field");
            // AJAX получаем данные в отсортированом виде
            $.ajax({
                type: "POST",
                dataType: "json",
                async: false,
                url: gBaseUrl + "lists/sorttable/",
                data: {
                    sort:gSortBy,
                    field:gSortField,
                    current_page:gCurrentPage,
                    search:gSearch,
                    itemsperpage:gItemsPerPage
                },
                success: function(data){
                    // При успешном AJAX
                    switch(data.result)
                    {
                        case "error":
                            alert(data.data);
                            break;
                        case "complete":
                            // Делаем перерисовку нашей таблицы
                            $("#checkAll tbody").html(data.data);
                            // Перерисовываем наш верхний пэйджинатор
                            $("#top_pagination").html(data.pagination);
                            // Перерисовываем наш нижний пэйджинатор
                            $("#bottom_pagination").html(data.pagination);
                            // обновляем счетчик общего количества записей в БД
                            $("#total_count_fields").html(data.firstNumber + " - " + data.lastNumber + " / " + data.totalCountInDB);
                            break;
                        default:
                            alert("Undefined error");
                            break;
                    } 
                }
            });
        }
    );
    
    //===== Check all checbboxes =====//
	
    $("#uniform-titleCheck input:checkbox").click(function() {
            var checkedStatus = this.checked;
            $("#checkAll tbody tr td:first-child input:checkbox").each(function() {
                    this.checked = checkedStatus;
                            if (checkedStatus == this.checked) {
                                    $(this).closest('.checker > span').removeClass('checked');
                                    $("#top_delete_check_fields").css("display","none");
                                    $("#bottom_delete_check_fields").css("display","none");
                            }
                            if (this.checked) {
                                    $(this).closest('.checker > span').addClass('checked');
                                    $("#top_delete_check_fields").css("display","inline-block");
                                    $("#bottom_delete_check_fields").css("display","inline-block");
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
    
   
   
    // Событие для клика по иконке EDIT
    $(".edit_field").live("click",function(){
        
        // Меняем контент модального окна
        $("#content-dialog-modal").html('<form><div class="clear"><label for="edit_name">Edit Name</label><input type="text" name="edit_name" id="edit_name" class="text ui-widget-content ui-corner-all autocomplete-off" /></div><div class="clear"><label for="edit_date_of_birth">Edit Date of Birth</label><input type="text" name="edit_date_of_birth" id="edit_date_of_birth" class="text ui-widget-content ui-corner-all autocomplete-off datepicker" /></div><div class="clear"><label for="edit_phone">Edit Phone</label><input type="text" name="edit_phone" id="edit_phone" class="text ui-widget-content ui-corner-all autocomplete-off" /></div><div class="clear"><label for="edit_email">Edit Email</label><input type="text" name="edit_email" id="edit_email" class="text ui-widget-content ui-corner-all autocomplete-off" /></div></form>');
        $("#content-dialog-modal").css("padding-top","0px");
        
        //===== Datepickers =====//
        // Так как контент динамический - мы делаем поиск елемента 
        // и пременяем к нему календарь
        $("#edit_date_of_birth").each(function() {
            $(this).datepicker({
                autoSize: true,
                dateFormat: 'yy-mm-dd'
            });
        });
        
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
                        // Вывод текущего значения полей из БД
                        $("#edit_name").val(data.data.name);
                        //$("#edit_date_of_birth").val(data.data.date_of_birth);
                        // Устанавливаем наш календарь на ту дату - которая лежит в БД
                        $("#edit_date_of_birth").datepicker( "setDate" , data.data.date_of_birth);
                        $("#edit_phone").val(data.data.phone);
                        $("#edit_email").val(data.data.email);
                        break;
                    default:
                        alert("Undefined error");
                        break;
                } 
            }
        });

        // Для контроля валидации и тултипов
        var tooltipEmail = false;
        var tooltipName = false;
        var tooltipPhone = false;
        var tooltipDateOfBirth = false;
        // Флаг для вычесления когда нужно закрыть окно
        // по кнопки Update Field
        var flagUpdateField = false;
                    
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
                            current_page:gCurrentPage,
                            sort:gSortBy,
                            field:gSortField,
                            search:gSearch,
                            itemsperpage:gItemsPerPage
                        },
                        success: function(data){

                            // Если AJAX успешен
                            switch(data.result)
                            {
                                case "error":
                                    alert(data.data);
                                    break;
                                case "error_valid":
                                    // Если на сервере не прошла валидация
                                    // делаем проверку на ошибку с email
                                    if(data.data.email)
                                    {
                                        // Присваиваем сообщение об ошибке
                                        $("#edit_email").attr("title",data.data.email);
                                        // Добавляем для поля Tooltip - вешаем выбранный нами триггер 'manual'
                                        $("#edit_email").tipsy({gravity: 's', trigger: 'manual'})
                                        // И сразу отображаем наш Tooltip с ошибкой
                                        $("#edit_email").tipsy("show");
                                        // отмечаем что для email появился тултип
                                        tooltipEmail = true;
                                    }
                                    else
                                    {
                                        // Проверяем создавали-ли мы тултип
                                        if(tooltipEmail)
                                        {
                                            // Если валидация прошла по этому елементу
                                            // закрываем тултип
                                            $("#edit_email").tipsy("hide");
                                        }
                                    }
                                    
                                    // делаем проверку на ошибку с name
                                    if(data.data.name)
                                    {
                                        // Присваиваем сообщение об ошибке
                                        $("#edit_name").attr("title",data.data.name);
                                        // Добавляем для поля Tooltip - вешаем выбранный нами триггер 'manual'
                                        $("#edit_name").tipsy({gravity: 's', trigger: 'manual'})
                                        // И сразу отображаем наш Tooltip с ошибкой
                                        $("#edit_name").tipsy("show");
                                        // отмечаем что для name появился тултип
                                        tooltipName = true;
                                    }
                                    else
                                    {
                                        // Проверяем создавали-ли мы тултип
                                        if(tooltipName)
                                        {
                                            // Если валидация прошла по этому елементу
                                            // закрываем тултип
                                            $("#edit_name").tipsy("hide");
                                        }
                                    }
                                    
                                    // делаем проверку на ошибку с phone
                                    if(data.data.phone)
                                    {
                                        // Присваиваем сообщение об ошибке
                                        $("#edit_phone").attr("title",data.data.phone);
                                        // Добавляем для поля Tooltip - вешаем выбранный нами триггер 'manual'
                                        $("#edit_phone").tipsy({gravity: 's', trigger: 'manual'})
                                        // И сразу отображаем наш Tooltip с ошибкой
                                        $("#edit_phone").tipsy("show");
                                        // отмечаем что для phone появился тултип
                                        tooltipPhone = true;
                                    }
                                    else
                                    {
                                        // Проверяем создавали-ли мы тултип
                                        if(tooltipPhone)
                                        {
                                            // Если валидация прошла по этому елементу
                                            // закрываем тултип
                                            $("#edit_phone").tipsy("hide");
                                        }
                                    }
                                    
                                    // делаем проверку на ошибку с date_of_birth
                                    if(data.data.date_of_birth)
                                    {
                                        // Присваиваем сообщение об ошибке
                                        $("#edit_date_of_birth").attr("title",data.data.date_of_birth);
                                        // Добавляем для поля Tooltip - вешаем выбранный нами триггер 'manual'
                                        $("#edit_date_of_birth").tipsy({gravity: 's', trigger: 'manual'})
                                        // И сразу отображаем наш Tooltip с ошибкой
                                        $("#edit_date_of_birth").tipsy("show");
                                        // отмечаем что для date_of_birth появился тултип
                                        tooltipDateOfBirth = true;
                                    }
                                    else
                                    {
                                        // Проверяем создавали-ли мы тултип
                                        if(tooltipDateOfBirth)
                                        {
                                            // Если валидация прошла по этому елементу
                                            // закрываем тултип
                                            $("#edit_date_of_birth").tipsy("hide");
                                        }
                                    }
                                    
                                    break;
                                case "complete":
                                    // устанавливаем - что мы прошли валидацию
                                    flagUpdateField = true;
                                    // Если вся валидация прошла успешна
                                    // закрываем наши тултипы
                                    
                                    // Проверяем создавали-ли мы тултип
                                    if(tooltipEmail)
                                    {
                                        // Если валидация прошла по этому елементу
                                        // закрываем тултип
                                        $("#edit_email").tipsy("hide");
                                    }
                                    
                                    // Проверяем создавали-ли мы тултип
                                    if(tooltipName)
                                    {
                                        // Если валидация прошла по этому елементу
                                        // закрываем тултип
                                        $("#edit_name").tipsy("hide");
                                    }
                                    
                                    // Проверяем создавали-ли мы тултип
                                    if(tooltipPhone)
                                    {
                                        // Если валидация прошла по этому елементу
                                        // закрываем тултип
                                        $("#edit_phone").tipsy("hide");
                                    }
                                    
                                    // Проверяем создавали-ли мы тултип
                                    if(tooltipDateOfBirth)
                                    {
                                        // Если валидация прошла по этому елементу
                                        // закрываем тултип
                                        $("#edit_date_of_birth").tipsy("hide");
                                    }
                                    
                                    // Делаем перерисовку нашей таблицы
                                    $("#checkAll tbody").html(data.data);
                                    // Перерисовываем наш верхний пэйджинатор
                                    $("#top_pagination").html(data.pagination);
                                    // Перерисовываем наш нижний пэйджинатор
                                    $("#bottom_pagination").html(data.pagination);
                                    // обновляем счетчик общего количества записей в БД
                                    $("#total_count_fields").html(data.firstNumber + " - " + data.lastNumber + " / " + data.totalCountInDB);
                                    
                                    break;
                                default:
                                    alert("Undefined error");
                                    break;
                            }
                        }
                    });
                    
                    if(flagUpdateField)
                    {
                        // И сразу закрываем мождальное окно
                        $(this).dialog("close");
                    }

                    return false;                
                },
                Cancel: function() {
                    // Если мы закрыли окно
                    // закрываем наши тултипы
                    // Проверяем создавали-ли мы тултип
                    if(tooltipEmail)
                    {
                        // Если валидация прошла по этому елементу
                        // закрываем тултип
                        $("#edit_email").tipsy("hide");
                    }

                    // Проверяем создавали-ли мы тултип
                    if(tooltipName)
                    {
                        // Если валидация прошла по этому елементу
                        // закрываем тултип
                        $("#edit_name").tipsy("hide");
                    }

                    // Проверяем создавали-ли мы тултип
                    if(tooltipPhone)
                    {
                        // Если валидация прошла по этому елементу
                        // закрываем тултип
                        $("#edit_phone").tipsy("hide");
                    }

                    // Проверяем создавали-ли мы тултип
                    if(tooltipDateOfBirth)
                    {
                        // Если валидация прошла по этому елементу
                        // закрываем тултип
                        $("#edit_date_of_birth").tipsy("hide");
                    }
                    
                    // Кнопка отмены 
                    $(this).dialog("close");
                    
                }
        },
        close: function(event, ui){ 
            
            // Если мы закрыли окно
            // закрываем наши тултипы
            // Проверяем создавали-ли мы тултип
            if(tooltipEmail)
            {
                // Если валидация прошла по этому елементу
                // закрываем тултип
                $("#edit_email").tipsy("hide");
            }

            // Проверяем создавали-ли мы тултип
            if(tooltipName)
            {
                // Если валидация прошла по этому елементу
                // закрываем тултип
                $("#edit_name").tipsy("hide");
            }

            // Проверяем создавали-ли мы тултип
            if(tooltipPhone)
            {
                // Если валидация прошла по этому елементу
                // закрываем тултип
                $("#edit_phone").tipsy("hide");
            }

            // Проверяем создавали-ли мы тултип
            if(tooltipDateOfBirth)
            {
                // Если валидация прошла по этому елементу
                // закрываем тултип
                $("#edit_date_of_birth").tipsy("hide");
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
                            current_page:gCurrentPage,
                            sort:gSortBy,
                            field:gSortField,
                            search:gSearch,
                            itemsperpage:gItemsPerPage
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
                                    // Перерисовываем наш верхний пэйджинатор
                                    $("#top_pagination").html(data.pagination);
                                    // Перерисовываем наш нижний пэйджинатор
                                    $("#bottom_pagination").html(data.pagination);
                                    // обновляем счетчик общего количества записей в БД
                                    $("#total_count_fields").html(data.firstNumber + " - " + data.lastNumber + " / " + data.totalCountInDB);
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
        
        if(gCurrentPage)
        {
            $.ajax({
                type: "POST",
                dataType: "json",
                async: false,
                url: gBaseUrl+ "lists/pagination",
                data: {
                    current_page:gCurrentPage,
                    sort:gSortBy,
                    field:gSortField,
                    search:gSearch,
                    itemsperpage:gItemsPerPage
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
                            // Перерисовываем наш верхний пэйджинатор
                            $("#top_pagination").html(data.pagination);
                            // Перерисовываем наш нижний пэйджинатор
                            $("#bottom_pagination").html(data.pagination);
                            // обновляем счетчик общего количества записей в БД
                            $("#total_count_fields").html(data.firstNumber + " - " + data.lastNumber + " / " + data.totalCountInDB);
                            break;
                        default:
                            alert("Undefined error");
                            break;
                    }
                }
            });
        }
    });
    
    
});