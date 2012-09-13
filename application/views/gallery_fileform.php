<form id="fileupload" action="<?php echo $baseUrl?>gallery/savefile/" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" id="id" value="<?php if(isset($fileinfo)){ echo $fileinfo['id']; } ?>" />
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-buttonbar">
        <div class="span7">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success fileinput-button">
                <i class="icon-plus icon-white"></i>
                <button><?php if(!isset($fileinfo))
                        {
                        ?>
                        Add file...
                        <?php
                        }
                        else
                        {
                        ?>
                        New file...
                        <?php 
                        }
                        ?></button><span id="filename"></span>
                <input type="file" name="file">
            </span>            
        </div>
    </div>
    <?php if(isset($fileinfo))
    {
        if($fileinfo['type'] == GALERYITEM_TYPE_VIDEO)
        {
            $fileinfo['file'] = str_replace(pathinfo($fileinfo['file'],PATHINFO_EXTENSION), 'jpg', $fileinfo['file']);
        }

    ?>
    <img alt="" src="<?php echo $baseUrl.'uploads/' . $fileinfo['id'].'sm_'.$fileinfo['file']; ?>" />
    <?php
    }
    ?>
    <div class="clear"></div>
    <div style="border: 1px solid black; height: 18px; width: 200px;">
        <div id="progressbar" style="height: 100%; width:0px; background: blue;"></div>
    </div>
    <br />
    <table>
        <tr>
            <td>Parent category</td>
            <td>
                <select name="pid" id="pid">
                    <option value="">--</option>
                    <?php foreach($categories as $v){ ?>
                    <option <?php if((isset($fileinfo) && $fileinfo['pid'] == $v['node_id'])){echo 'selected="selected"'; }?> value="<?php echo $v['node_id']; ?>" ><?php for($i=0; $i<$v['numlevel']; $i++){echo '&nbsp;&nbsp;&nbsp;';} echo $v['title']; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Title</td>
            <td><input type="text" id="title" name="title" value="<?php if(isset($fileinfo)){ echo $fileinfo['title']; } ?>" /></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><textarea cols="0" id="description" rows="0" name="description"><?php if(isset($fileinfo)){ echo $fileinfo['description'];}?></textarea></td>
        </tr>
    </table>
</form>