<table>
        <tr>
            <td>Parent category</td>
            <td>
                <select name="pidtomove" id="pidtomove">
                    <option value="">--</option>
                    <?php foreach($categories as $v){ ?>
                    <option value="<?php echo $v['node_id']; ?>" ><?php for($i=0; $i<$v['numlevel']; $i++){echo '&nbsp;&nbsp;&nbsp;';} echo $v['title']; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
</table>
