// Функция для перезагрузки основных данных на странице - если данные не найдены
function reload_data_per_page_empty_data(data)
{
    // Показываем что записей по этому запросу не найдено
    $("#empty_database").html(data.data);
    // При изменении данных на странице убираем выделыние 
    // верхнего чекбокса, который отвечает за выделение всех чекбоксов на странице
    $("#uniform-titleCheck span").removeClass('checked');
    // И удаляем кнопки для удаление полей
    $("#top_delete_check_fields").css("display","none");
    $("#bottom_delete_check_fields").css("display","none");
    // Убираем данные в нашей таблицы
    $("#checkAll tbody").html("");
    // Убираем наш верхний пэйджинатор
    $("#top_pagination").html("");
    // Убираем наш нижний пэйджинатор
    $("#bottom_pagination").html("");
    // Убираем счетчик общего количества записей в БД
    $("#total_count_fields").html("");
}

var gCurrentPage = 0;
var gNoMoreScroll = false;
var gCurrentlyLoading = false;

$(document).ready(function () {
    $(window).scroll(function () {
      if (!gNoMoreScroll && !gCurrentlyLoading && ($(document).height() - $(window).height() <= $(window).scrollTop() + 50)) {
         gCurrentlyLoading = true;
         dynamic_table_get_more();
      }
    });
    //Сортировка данных столбца
    dynamic_table_sort_column();
    //Поиск по таблице
    dynamic_table_search_in_table();
    //Групповое удаление полей
    dynamic_table_delete_all_check_field();
});

// Callback функция успешной отработки AJAX 
// Удаления нескольких записей
function success_delete_fields(data)
{
    // При успешном AJAX
    switch(data.result)
    {
        case "error":
            alert(data.data);
            break;
        case "complete":
            // Показываем что записей после удаления не найдено
            $("#empty_database").html("");
            // При изменении данных на странице убираем выделыние
            // верхнего чекбокса, который отвечает за выделение всех чекбоксов на странице
            $("#uniform-titleCheck span").removeClass('checked');
            $("#uniform-titleCheck input:checkbox").removeAttr("checked");
            // И удаляем кнопки для удаление полей
            $("#top_delete_check_fields").css("display","none");
            // Делаем перерисовку нашей таблицы
            $("#checkAll tbody").html(data.data);
            break;
        case "empty_database":
            // Показываем что записей после удаления не найдено
            $("#empty_database").html(data.data);
            // При изменении данных на странице убираем выделыние
            // верхнего чекбокса, который отвечает за выделение всех чекбоксов на странице
            $("#uniform-titleCheck span").removeClass('checked');
            $("#uniform-titleCheck input:checkbox").removeAttr("checked");
            // И удаляем кнопки для удаление полей
            $("#top_delete_check_fields").css("display","none");
            // Убираем данные в нашей таблицы
            $("#checkAll tbody").html("");
            break;
        default:
            alert("Undefined error");
            break;
    }
}
// Callback функция успешной отработки AJAX 
// удаление одного поля
function success_delete_one_field(data)
{
    // Если AJAX успешен
    switch(data.result)
    {
        case "error":
            alert(data.data);
            break;
        case "complete":
                // Показываем что записей после удаления найдено
            $("#empty_database").html("");
            $("#uniform-titleCheck span").removeClass('checked');
            $("#uniform-titleCheck input:checkbox").removeAttr("checked");
            // Удаляем это поле из дома
            lRow.remove();
            break;
            case "empty_database":
            // Показываем что записей после удаления не найдено
            $("#empty_database").html(data.data);
            // При изменении данных на странице убираем выделыние
            // верхнего чекбокса, который отвечает за выделение всех чекбоксов на странице
            $("#uniform-titleCheck span").removeClass('checked');
            $("#uniform-titleCheck input:checkbox").removeAttr("checked");
            // И удаляем кнопки для удаление полей
            $("#top_delete_check_fields").css("display","none");
            // Убираем данные в нашей таблицы
            $("#checkAll tbody").html("");
            break;
        default:
            alert("Undefined error");
            break;
    }
}
// Callback функция успешной отработки AJAX 
// Поиск по данным 
function success_search(data)
{
    // При успешном AJAX
    switch(data.result)
    {
        case "error":
            alert(data.data);
            break;
        case "empty_database":
            reload_data_per_page_empty_data(data);
            break;
        case "complete":
            // Показываем что записей по этому запросу найдено
            $("#empty_database").html("");
            // При изменении данных на странице убираем выделыние
            // верхнего чекбокса, который отвечает за выделение всех чекбоксов на странице
            $("#uniform-titleCheck span").removeClass('checked');
            $("#uniform-titleCheck input:checkbox").removeAttr("checked");
            // И удаляем кнопки для удаление полей
            $("#top_delete_check_fields").css("display","none");
            // Делаем перерисовку нашей таблицы
            $("#checkAll tbody").html(data.data);
            break;
        default:
            alert("Undefined error");
            break;
    }
}

// Callback функция успешной отработки AJAX 
// Редактирования записи
function success_dynamic_table_get_more(data)
{
    // Если AJAX успешен
    switch(data.result)
    {
        case "error":
            alert(data.data);
            break;
        case "complete":
            dynamic_table_add_results(data.data);
            // Добавление/удаление всех чекбоксов
            // с учетом динамической подгрузки страницы
            if($(".titleIcon span").hasClass("checked"))
            {
                dynamic_table_add_more_checkbox($(".titleIcon span").hasClass("checked"));
            }
            break;
        default:
            alert("Undefined error");
            break;
    }
}
function success_dynamic_table_sorttable(data)
{
    // При успешном AJAX
    switch(data.result)
    {
        case "error":
            alert(data.data);
            break;
        case "complete":
            // Делаем перерисовку нашей таблицы
            $("#checkAll tbody").html(data.data);
            break;
        default:
            alert("Undefined error");
            break;
    }
}

// Удаление всех выбранных полей
function dynamic_table_delete_all_check_field()
{
    // Удалить все выделенные чекбоксы одной кнопкой
    $("#top_delete_check_fields").live("click",function(){
        // Массив для хранения ID записи
        var arrFieldsId = [];
        // Пробегаемся по все чекбоксам на странице
        $("#checkAll tbody tr td:first-child input:checkbox").each(function()
        {
            // Если чекбокс выделен
            if(this.checked)
            {
                // Записываем в конец массива ID строки, которую нужно удалить
                arrFieldsId[arrFieldsId.length] = $(this).parent().parent().parent().next().html();
            }

        });
        // Меняем контент модального окна
        $("#content-dialog-modal").html("Are you sure you want to remove this fields?");
        $("#content-dialog-modal").css("padding-top","20px");

        // Показываем диалоговое окно с подверждением удаления
        $("#dialog-modal" ).dialog({
            title: "Delete Field",
            height: 160,
            width: 265,
            modal: true,
            hide: "explode",
            buttons: {
                "Delete Field": function() {
                    // Если пользователь нажал удалить
                    // Отправляем запрос на удаления, отправляем массив с ID полей
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        async: false,
                        url: gBaseUrl + "listsdynamic/deletesomefields/",
                        data: {
                            somefields:arrFieldsId,
                            sort:gSortBy,
                            field:gSortField,
                            search:gSearch
                        },
                        success: success_delete_fields
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
}

// Функция поиска в БД и динамическое изменение талицы
function dynamic_table_search_in_table()
{
    $("#search_in_table").keypress(function(e){
        if(e.keyCode==13)
        {
            gSearch = $("#search_in_table").val();
            // AJAX получаем данные с применением поисковой фразы
            $.ajax({
                type: "POST",
                dataType: "json",
                async: false,
                url: gBaseUrl + "listsdynamic/search/",
                data: {
                    search:gSearch,
                    sort:gSortBy,
                    field:gSortField,
                    page_to_load: gCurrentPage + 1
                },
                success: success_search
            });
        }
    });
}


function dynamic_table_sort_column()
{
    $(".sortCol").toggle(
        function()
        {
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
            
            //Сортировка таблицы
            dynamic_table_sorttable(this);
        },
        function()
        {
            // Ищем все елементы у которых присутствуют стрелочки
            // И убираем класы верхней и нижней стрелочки :)
            $(".sortCol").each(function() {
                $(this).removeClass("headerSortUp");
                $(this).removeClass("headerSortDown");
            });
            // Добавляем класс показа сортировки DESC
            $(this).addClass("headerSortUp");
            // Сохраняем тип сортировки в глобально переменной
            // чтобы можно было использовать при паджинации
            gSortBy = "desc";

            //Сортировка таблицы
            dynamic_table_sorttable(this);
        }
    );
}


function dynamic_table_sorttable(lColumn)
{
            // Сохраняем имя поля -по которому нужно сортировать
            gSortField = $(lColumn).attr("field");
            // AJAX получаем данные в отсортированом виде
            $.ajax({
                type: "POST",
                dataType: "json",
                async: false,
                url: gBaseUrl + "listsdynamic/sorttable/",
                data: {
                    search:gSearch,
                    sort:gSortBy,
                    field:gSortField,
                    page_to_load: gCurrentPage + 1
                },
                success: success_dynamic_table_sorttable
            });
}

function dynamic_table_get_more()
{  
    $.ajax({
        type: "POST",
        dataType: "json",
        async: false,
        url: gBaseUrl + "listsdynamic/getmore",
        data: {
            search:gSearch,
            page_to_load: gCurrentPage + 1,
            sort:gSortBy,
            field:gSortField
        },
        success: success_dynamic_table_get_more
        
    });
}

function dynamic_table_add_more_checkbox(checkedStatus)
{
    // Добавление/удаление всех чекбоксов
    // с учетом динамической подгрузки страницы
    $("#checkAll tbody tr td:first-child input:checkbox").each(function() {
            this.checked = checkedStatus;
                    if (checkedStatus == this.checked) {
                            $(this).closest('.checker > span').removeClass('checked');
                    }
                    if (this.checked) {
                            $(this).closest('.checker > span').addClass('checked');
                    }
    });
}

function dynamic_table_add_results(data)
{
   if (data != '')
   {
      $('.sTable').append(data);
      gCurrentPage ++;
      setTimeout('dynamic_table_set_currently_loading_false()', 250);
   }
   else
   {
      gNoMoreScroll = true;
   }
}

function dynamic_table_set_currently_loading_false()
{
   gCurrentlyLoading = false;
}

$(document).ready(function(){
     
    // for not autocomplete browser
    $("input.autocomplete-off").attr("autocomplete", "off");
    
    /* Tables
================================================== */
    //===== Check all checbboxes =====//
	
    $(".titleIcon input:checkbox").click(function() {
            var checkedStatus = this.checked;
            dynamic_table_add_more_checkbox(checkedStatus);
    });	

    $('#checkAll tbody tr td:first-child').next('td').css('border-left-color', '#CBCBCB');
    //===== Check one checbbox =====//
    $(".checker input:checkbox").live("click",function()
    {
        // Сохраняем текущий статус
        var checkedStatus = this.checked;

        if (checkedStatus == this.checked)
        {
            $(this).closest('.checker > span').removeClass('checked');
        }
        if (this.checked)
        {
            $(this).closest('.checker > span').addClass('checked');
            $("#top_delete_check_fields").css("display","inline-block");
        }
        // Если нет ни одного выделенного чекбокса
        if($(".checker :checked").length == 0)
        {
            // Прячем наши кнопки удалить
            $("#top_delete_check_fields").css("display","none");
        }
    });
    //===== Datepickers =====//
	
    $( ".datepicker" ).datepicker({ 
        autoSize: true,
        dateFormat: 'yy-mm-dd'
    });
   
   
    // Событие для клика по иконке EDIT
    $(".edit_field").live("click",function(element){
       //затрагиваемая строка таблицы
       var lRow = $(this).parent().parent();

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
            url: gBaseUrl + "listsdynamic/getfield/"+fieldId,
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
                        url: gBaseUrl + "listsdynamic/updatefield/",
                        data: {
                            search:gSearch,
                            name:$("#edit_name").val(),
                            date_of_birth:$("#edit_date_of_birth").val(),
                            phone:$("#edit_phone").val(),
                            email:$("#edit_email").val(),
                            fieldId:fieldId,
                            current_page:gCurrentPage,
                            sort:gSortBy,
                            field:gSortField
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
                                    $("#uniform-titleCheck span").removeClass('checked');
                                    $("#uniform-titleCheck input:checkbox").removeAttr("checked");
                                    // Делаем перерисовку нашей таблицы
                                    $("#checkAll tbody").html(data.data);
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
        var lRow = $(this).parent().parent();
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
                    var somefields = [];
                    somefields[somefields.length] = fieldId;
                    // Если пользователь нажал удалить
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        async: false,
                        url: gBaseUrl + "listsdynamic/deletesomefields/",
                        data: {
                            somefields:somefields,
                            current_page:gCurrentPage,
                            search:gSearch,
                            sort:gSortBy,
                            field:gSortField 
                        },
                        success: success_delete_one_field
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
    
    
    
    
    
});