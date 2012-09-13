<form id="categoryform">
    <input type="hidden" name="id" id="id" value="<?php if(isset($categoryinfo)){ echo $categoryinfo['id']; } ?>" />
    <table>
        <tr>
            <td>Parent category</td>
            <td>
                <select name="pid" id="pid">
                    <option value="">--</option>
                    <?php foreach($categories as $v){ ?>
                    <option <?php if(isset($categoryinfo) && $categoryinfo['pid'] == $v['node_id']){echo 'selected="selected"'; }?> value="<?php echo $v['node_id']; ?>" ><?php for($i=0; $i<$v['numlevel']; $i++){echo '&nbsp;&nbsp;&nbsp;';} echo $v['title']; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Title</td>
            <td><input type="text" id="title" name="title" value="<?php if(isset($categoryinfo)){ echo $categoryinfo['title']; } ?>" /></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><textarea cols="0" id="description" rows="0" name="description"><?php if(isset($categoryinfo)){ echo $categoryinfo['description'];}?></textarea></td>
        </tr>
    </table>
</form>