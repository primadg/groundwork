var galleryHandler =function()
{
    this.parent_id = null;
    var self = this;
    this.page = 1;
    this.order = 'numleft';
    this.dirrection = 'asc';
    this.pagesingle = 1;
    this.only_direct = 1;
    this.files = [];
    this.fileids = [];
    this.nextid = null;
    this.previd = null;
    this.pre_load = function()
    {
        $('#rightSide').css('opacity', '0.4');
    }
    this.after_load = function()
    {
        $('#rightSide').css('opacity', '1');
    }
    this.display_errors = function(errors_data)
    {
        $('.errmessage').remove();
        for(var i =0; i<errors_data.length; i++)
        {
            $("#"+errors_data[i]['field']).css('border-color', 'red');
            $('<span style="position:absolute; color:red; top: 40px; left: 245px;" class="errmessage">'+errors_data[i]['message']+'</span>').insertAfter("#"+errors_data[i]['field']);
        }
    }
    this.load_categories = function(id)
    {
        if(id)
        {
            this.parent_id = id;
        }
        
        self.pre_load();
        location.hash = '#'+this.parent_id;
        $.ajax({
            url:gBaseUrl + 'gallery/categorylist/' + self.parent_id,
            success:function(data)
            {
                self.after_load();
                if(data)
                {
                    $("#categorylist").html(data);
                }
                else
                {
                    /**
                     * @todo: error handler
                     */
                }
                self.load_files(self.parent_id);
                self.load_singlefiles();
            }
        });
    }
    this.load_files = function(id)
    {
        self.pre_load();
        $.ajax({
            url:gBaseUrl + 'gallery/filelist/' + id,
            data:{
                page:self.page,
                order: self.order,
                dirrection:self.dirrection,
                direct: self.only_direct
            },
            type:'POST',
            success:function(data)
            {
                self.after_load();
                $("#filelist").html(data);
            }
        });
    }
    this.load_singlefiles = function()
    {
        self.pre_load();
        $.ajax({
            url:gBaseUrl + 'gallery/filelist/',
            data:{
                page:self.pagesingle,
                order: self.order,
                dirrection:self.dirrection,
                direct: self.only_direct
            },
            type:'POST',
            success:function(data)
            {
                self.after_load();
                $("#filelistempty").html(data);
            }
        });
    }
    this.load_page = function(page)
    {
        self.page = page;
        self.load_files(self.parent_id);
    }
    this.load_pagesingle = function(page)
    {
        self.pagesingle = page;
        self.load_singlefiles();
    }
    this.show_direct = function(flag)
    {
        self.only_direct = flag;
        self.page = 1;
        self.load_files(self.parent_id);
    }
    this.delete_item = function(id)
    {
        $.ajax({
            type:'POST',
            url:gBaseUrl + 'gallery/deleteitem/',
            data:{item_id:id},
            success:function()
            {
                self.load_files(self.parent_id);
                self.load_categories(self.parent_id);
                self.load_singlefiles();
            }
        });
    }
    this.category_form = function(cat_id)
    {
        if(!cat_id)
        {
            cat_id = '';
        }
        $("#dialog").html('<div class="preloader"></div>');
        $.ajax({
            url: gBaseUrl + 'gallery/categoryform/'+cat_id,
            type: 'POST',
            success: function(data)
            {
                $("#dialog").html(data);
                setTimeout(function(){
                    if(!cat_id)
                    {
                        $("#pid").val(self.parent_id);
                    }
                },50);
            }
        });
        $("#dialog").dialog({
            title: (cat_id==''?"Add category":'Edit category'),
            height: 390,
            width: 360,
            modal: true,
            hide: "explode",
            buttons:
            {
                'Save':function(){
                    var self = $(this);
                    $.ajax({
                        url:gBaseUrl + 'gallery/savecategory/',
                        data: $("#categoryform").serialize(),
                        type: 'POST',
                        dataType: 'JSON',
                        success:function(data)
                        {
                            if(!data.error)
                            {
                                self.dialog("close");
                                $("#dialog").html('');
                                gallery.load_categories();
                            }
                            else
                            {
                                gallery.display_errors(data.errors);
                            }

                        }
                    });

                 },
                'Cancel':function(){
                    $(this).dialog("close");
                    $("#dialog").html('');
                }
            }
        });
        return false;
    }
    this.file_form = function(f_id)
    {
        if(!f_id)
        {
            f_id = '';
        }
        $("#dialog").html('<div class="preloader"></div>');
        $.ajax({
            url: gBaseUrl + 'gallery/fileform/'+f_id,
            type: 'POST',
            success: function(data)
            {
                $("#dialog").html(data);
                if(!f_id && self.parent_id)
                {
                    $("#pid").val(self.parent_id)
                }
                'use strict';
                $('#fileupload').fileupload({
                        add:function(e, data){
                            self.files = [];
                            self.files[0] = data.files[0];
                            $("#filename").html(data.files[0].name);
                        },
                        progress:function(event)
                        {
                            var width = Math.round((event.loaded/event.total)*100);
                            $("#progressbar").css('width', width+'%');
                            if(width == 100)
                            {
                                $("#progressbar").parent().html('Processing...');
                            }
                        },
                        redirect:window.location.href.replace(
                        /\/[^\/]*$/,
                        '/cors/result.html?%s'
                        ),
                        acceptFileTypes:/^image|video\/(gif|jpe?g|png|mp4|flv|x-flv|avi|mpe?g)$/
                });
            }
        });
        $("#dialog").dialog({
            title: (f_id==''?"Add file":'Edit file'),
            height: 390,
            width: 360,
            modal: true,
            hide: "explode",
            buttons:
            {
                'Save':function(){
                    if(f_id)
                    {
                        if(self.files.length)
                        {
                            $('#fileupload').fileupload('send', {files: self.files}).complete(function(){
                                    $('#dialog').dialog("close");
                                    self.load_files(self.parent_id);
                                    self.load_singlefiles();
                                });
                        }
                        else
                        {
                            $.ajax({
                                url:gBaseUrl + 'gallery/savefile',
                                type:'POST',
                                data:$("#fileupload").serialize(),
                                success:function(data)
                                {
                                    if(!data.error)
                                    {
                                        $('#dialog').dialog("close");
                                        self.load_files(self.parent_id);
                                        self.load_singlefiles();
                                    }
                                }
                            });
                        }
                    }
                    else
                    {
                        if(self.files.length)
                        {
                        $('#fileupload').fileupload('send', {files: self.files}).complete(function(){
                                    $('#dialog').dialog("close");
                                    self.load_files(self.parent_id);
                                    self.load_singlefiles();
                                    self.files = [];
                                });
                        }
                        else
                        {
                            $('.errorspan').remove();
                            $("#fileupload").html('<span class="errorspan" style="color:red;">Choose a file please</span>'+$("#fileupload").html());
                        }
                    }
                },
                'Cancel':function(){
                    $(this).dialog("close");
                    $("#dialog").html('');
                }
            }
        });
        return false;
    }
    this.edit_gallery = function(id)
    {
        return self.category_form(id);
    }
    this.delete_selected = function()
    {
        $("#dialog").html('');
        $("#dialog").dialog({
            title: 'Are you sure to delete selected files?',
            height: 190,
            width: 360,
            modal: true,
            hide: "explode",
            buttons:
            {
                'Delete':function(){
                    $.ajax({
                        url:gBaseUrl + 'gallery/deleteitems',
                        data:{items:self.fileids},
                        type:'POST',
                        success:function(){
                            self.load_files(self.parent_id);
                            self.load_singlefiles();
                            $("#dialog").dialog('close');
                            $("#dialog").html('');
                        }
                    });
                },
                'Cancel':function(){
                    $(this).dialog("close");
                    $("#dialog").html('');
                }
            }
        });
    }
    this.move_selected = function()
    {
        $("#dialog").html('<div class="preloader"></div>');
        $.ajax({
            url:gBaseUrl + 'gallery/setparentform',
            type:'GET',
            success:function(data){
                $("#dialog").html(data);
            }
        });
        $("#dialog").dialog({
            title: 'Move files to',
            height: 190,
            width: 360,
            modal: true,
            hide: "explode",
            buttons:
            {
                'Move':function(){                    
                    $.ajax({
                        url:gBaseUrl + 'gallery/moveitems',
                        data:{
                            items:self.fileids,
                            pid:$("#pidtomove").val()
                        },
                        type:'POST',
                        success:function(){
                            self.load_files(self.parent_id);
                            self.load_singlefiles();
                            $("#dialog").dialog("close");
                            $("#dialog").html('');
                        }
                    });
                },
                'Cancel':function(){
                    $(this).dialog("close");
                    $("#dialog").html('');
                }
            }
        });
    }
    this.showfile = function(f_id)
    {
        this.fill_filedata(f_id);
        $("#dialog").dialog({
            title: 'Loading...',
            height: 580,
            width: 650,
            modal: true,
            hide: "explode",
            buttons:
            {
                '< PREV':function(){
                    
                        self.fill_filedata(self.previd);
                    
                },
                'CLOSE':function(){
                    $(this).dialog('close');
                    $("#dialog").html('');
                },
                'NEXT >':function(){
                    
                        
                        self.fill_filedata(self.nextid);
                    
                }
            }
        });
    }
    this.fill_filedata = function(id)
    {
        $("#dialog").html('<div class="preloader"></div>');
        $("#dialog").dialog('option', 'title', 'Loading...');
        $.ajax({
            url:gBaseUrl + 'gallery/viewitem/'+id,
            type:'POST',
            data:{direct: parseInt(gallery.only_direct), parent:self.parent_id},
            dataType:'JSON',
            success:function(data){
                $("#dialog").html(data.html);
                self.previd = data.previd;
                self.nextid = data.nextid;
                $("#dialog").dialog('option', 'title', data.title);
            }
        });
    }
}
gallery = new galleryHandler();
$(document).ready(function(){
    var hash = location.hash.replace('#', '');
    
    if(parseInt(hash))
    {
        gallery.parent_id = hash;
    }
    
    gallery.load_categories(gallery.parent_id);
    gallery.load_singlefiles();
    $('.galleryitem').live('mouseenter', function(){
        $(this).find('.actions').each(function(){
            $(this).show();
        });
    });
    $('.galleryitem').live('mouseleave', function(){
        $(this).find('.actions').each(function(){
            $(this).fadeOut(300);
        });
    });
    $('ul.ppages li a').live('click', function(){
        var pg = $(this).attr('page');
        if(pg)
        {
            gallery.load_page(pg);
        }
        return false;
    });
    $('ul.epages li a').live('click', function(){
        var pg = $(this).attr('page');
        if(pg)
        {
            gallery.load_pagesingle(pg);
        }
        return false;
    });
    $("#direct_flag").live('change', function(){
        if($(this).attr('checked') == 'checked')
        {
            flag = 1;
        }
        else
        {
            flag = 0;
        }
        gallery.show_direct(flag);
        return true;
    });
    $('.update').live('click', function(){
        gallery.file_form($(this).attr('id'));
    });
    $('.delete').live('click', function(){
        var itemid = $(this).attr('id');
        $("#dialog").html('<div>Are you sure to delete this file?</div>');
        $("#dialog").dialog({
            title: "Confirm File Delete",
            height: 190,
            width: 360,
            modal: true,
            hide: "explode",
            buttons:
            {
                'Yes':function(){
                    gallery.delete_item(itemid);
                    $(this).dialog("close");
                    $("#dialog").html('');
                 },
                'Cancel':function(){
                    $(this).dialog("close");
                    $("#dialog").html('');
                }
            }
        });
    });
    $('.deletecategory').live('click', function(){
        var itemid = $(this).attr('id');
        $("#dialog").html('<div>Are you sure to delete this category?</div>');
        $("#dialog").dialog({
            title: "Confirm File Delete",
            height: 190,
            width: 360,
            modal: true,
            hide: "explode",
            buttons:
            {
                'Yes':function(){
                    gallery.delete_item(itemid);
                    $(this).dialog("close");
                    $("#dialog").html('');
                 },
                'Cancel':function(){
                    $(this).dialog("close");
                    $("#dialog").html('');
                }
            }
        });
    });
    $('.addcategory').live('click',function(){
        gallery.category_form();
    });
    $('.addfile').live('click',function(){
        gallery.file_form();
    });
    $("#filelistall").live('click', function(){
        $('.filelistchecker').each(function(){
            if($("#filelistall").attr('checked') == 'checked')
            {
                gallery.fileids[gallery.fileids.length] = $(this).val();
                $(this).attr('checked', 'checked');
            }
            else
            {
                gallery.fileids = [];
                $(this).removeAttr('checked');
            }
        });
    });
    $('.filelistchecker').live('click', function(){
        gallery.fileids = [];
        $('.filelistchecker').each(function(){
            if($(this).attr('checked'))
            {
                gallery.fileids[gallery.fileids.length] = $(this).val();
            }
        });
    });

    $("#actionset").live('change', function(){
        switch($(this).val())
        {
            case 'Delete':
            {
                gallery.delete_selected();
            }break;
            case 'Move':
            {
                gallery.move_selected();
            }break;
            default:
            {

            }
        }
    });
    $('.sorter').live('click', function(){
            var field = $(this).attr('sort');
            if(gallery.order == field)
            {
                if(gallery.dirrection == 'ASC')
                {
                    gallery.dirrection = 'DESC';
                }
                else
                {
                    gallery.dirrection = 'ASC';
                }
            }
            else
            {
                gallery.order = field;
                gallery.dirrection = 'ASC';
            }
            gallery.load_files(gallery.parent_id);
            gallery.load_singlefiles();
        });
});