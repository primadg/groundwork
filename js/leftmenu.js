$(document).ready(function(){
     
    // for not autocomplete browser
    $("input.autocomplete-off").attr("autocomplete", "off");
    
    // Пробегаемся по елементам нашего левого меню
    $("#menu li ul.sub").each(function(){
        // Изначально прячим все подменю 
        $(this).css("display","none");
        // И показываем количество детей в каждом меню
        $(this).prev("a").children("strong").html($(this).children().length);
        
    });
    // При клике на пункт меню
    $("#menu li").live("click",function(){
        // Делаем все елементы меню не активны
        $("#menu li > a").each(function(){
            $("#menu li > a").removeClass("active").addClass("inactive");
            // И прячем открытые подменю
            $("#menu li ul.sub").css("display","none");
        });
        // Потом делаем активным тот пункт меню, на который нажали
        $(this).children("a").removeClass("inactive").addClass("active");
        // И если есть подменю - показываем его
        $(this).children("ul.sub").css("display","block");
    });
    
});