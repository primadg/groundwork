var aclmanager = function()
{
    var self = this;
    this.showerrors = function(errors)
    {
        for(var i in errors)
        {
            var input = $('#dialog [name='+errors[i].field+']');
            input.attr("title",errors[i].message);
            input.tipsy({gravity:'s', trigger: 'manual'});
            input.tipsy('show');
        }
        return false;
    }
    var roleform = function(roledata)
    {
        var dialog = $("#dialog");
        dialog.html($("#roleform").html());
        dialog.dialog({
            title: 'Role info',
            modal: true,
            hide: "explode",
            buttons:
            {
                'Save':function(){
                    $.ajax({
                        url:gBaseUrl+'aclmanager/saverole',
                        type:'POST',
                        dataType:'JSON',
                        data: {
                            name:$("#dialog #rolename").val(),
                            description: $("#dialog #roledescription").val(),
                            id: $("#dialog #roleid").val()
                        },
                        success: function(data)
                        {
                            if(data.errors.length)
                            {
                                for(var i in data.errors)
                                {
                                    data.errors[i].field = 'role'+data.errors[i].field;
                                }
                                self.showerrors(data.errors);
                            }
                            else
                            {
                                $('#dialog').dialog('close');
                                $('#dialog').html('');
                                $('.tipsy').remove();
                                self.loadroles();
                            }
                        }
                    });

                },
                'Cancel': function(){
                    $(this).dialog('close');
                    $('#dialog').html('');
                    $('.tipsy').remove();
                }
            }
        });
        if(roledata)
        {
            setTimeout(function(){
                $("#dialog #rolename").val(roledata.name);
                $("#dialog #roleid").val(roledata.id);
                $("#dialog #roledescription").val(roledata.description);
            }, 10);
        }
        return false;
    }
    this.showroleform = function(roleid)
    {
        if(roleid)
        {
            $.ajax({
                url: gBaseUrl+'aclmanager/roleform',
                type:'POST',
                data: {role_id:roleid},
                dataType: 'JSON',
                success: function(data)
                {
                    roleform(data.role);
                }
            });
        }
        else
        {
            roleform();
        }
        return false;
    }

    this.deleterole = function(roleid)
    {
        $("#dialog").dialog({
            title: 'Are you sure to delete this role?',
            modal: true,
            hide: "explode",
            buttons:
            {
                'Yes':function(){
                    $.ajax({
                        url: gBaseUrl+'aclmanager/deleterole',
                        type:'POST',
                        data: {role_id:roleid},
                        dataType: 'JSON',
                        success: function(data)
                        {
                            if(!data.error)
                            {
                                $("#dialog").dialog('close');
                                $('#dialog').html('');
                                self.loadroles();
                            }
                            else
                            {
                                self.showerrors(data.errors);
                            }
                        }
                    });
                },
                'Cancel': function(){
                    $(this).dialog('close');
                    $('#dialog').html('');
                }
            }
        });
        return false;
    }
    
    this.permissionform = function(pid)
    {
        $.ajax({
            url:gBaseUrl+'aclmanager/permissionform',
            type: 'POST',
            dataType: 'html',
            data:{permission_id:pid},
            success:function(data){
                var dialog = $("#dialog");
                dialog.html(data);
                dialog.dialog({
                    title: 'Permission',
                    modal: true,
                    hide: "explode",
                    buttons:
                    {
                        'Save':function(){
                            $.ajax({
                                url:gBaseUrl+'aclmanager/savepermission',
                                type: 'POST',
                                dataType:'JSON',
                                data:
                                {
                                    parent:$("#dialog #parent").val(),
                                    id:$("#dialog #id").val(),
                                    name:$("#dialog #name").val(),
                                    description:$("#dialog #description").val()
                                },
                                success:function(data){
                                    if(data.errors.length)
                                    {
                                        self.showerrors(data.errors);
                                    }
                                    else
                                    {
                                        $("#dialog").dialog('close');
                                        $('#dialog').html('');
                                        self.loadpermissions();
                                    }
                                }
                            });
                        },
                        'Cancel':function(){
                            $(this).dialog('close');
                            $('#dialog').html('');
                        }
                    }
                });
            }
        });
        return false;
    }

    this.savepermission = function()
    {
        return false;
    }

    this.deletepermission = function(permid)
    {
        $("#dialog").html('Are you sure to delete this permission?');
        $("#dialog").dialog({
            title: 'Confirmation',
            modal: true,
            hide: "explode",
            buttons:
            {
                'Yes':function(){
                    $.ajax({
                        url: gBaseUrl+'aclmanager/deletepermission',
                        type:'POST',
                        data: {permission_id:permid},
                        dataType: 'JSON',
                        success: function(data)
                        {
                            if(!data.error)
                            {
                                $("#dialog").dialog('close');
                                $('#dialog').html('');
                                self.loadpermissions();
                            }
                            else
                            {
                                self.showerrors(data.errors);
                            }
                        }
                    });
                },
                'Cancel': function(){
                    $(this).dialog('close');
                    $('#dialog').html('');
                }
            }
        });
        return false;
    }

    this.loadroles = function()
    {
        $.ajax({
            url: gBaseUrl+'aclmanager',
            type:'GET',
            dataType:'html',
            success:function(data)
            {
                $("#ajaxcontent").html(data);
            }
        });
        return false;
    }

    this.loadpermissions = function()
    {
        $.ajax({
            url: gBaseUrl+'aclmanager/permissions',
            type:'GET',
            dataType:'html',
            success:function(data)
            {
                $("#ajaxcontent").html(data);
            }
        });
        return false;
    }

    this.rolepermissions = function(roleid)
    {
        $.ajax({
            url: gBaseUrl+'aclmanager/rolepermissions/'+roleid,
            type:'GET',
            dataType:'html',
            success:function(data)
            {
                $("#dialog").html(data);
                $("#dialog").dialog({
                    title: 'Role permissions',
                    modal: true,
                    hide: "explode",
                    width:'400px',
                    buttons:
                    {
                        'Save':function(){
                            $.ajax({
                                url: gBaseUrl+'aclmanager/saverolepermissions/'+roleid,
                                type:'POST',
                                data: $("#rolepermissionsform").serialize(),
                                success: function(data)
                                {
                                    $("#dialog").dialog('close');
                                    $('#dialog').html('');
                                }
                            });
                        },
                        'Cancel': function(){
                            $(this).dialog('close');
                            $('#dialog').html('');
                        }
                    }
                });
            }
        });
    }
}
var acl = new aclmanager();