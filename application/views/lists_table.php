<?php
foreach ($lists as $key => $value)
{
?>
<tr>
    <td>
        <div class="checker">
            <span class="">
                <input type="checkbox" name="checkRow" style="opacity: 0; ">
            </span>
        </div>
    </td>
    <td><?php echo $value['id']; ?></td>
    <td><?php echo $value['name']; ?></td>
    <td><?php echo $value['date_of_birth']; ?></td>
    <td><?php echo $value['phone']; ?></td>
    <td><?php echo $value['email']; ?></td>
    <td>
        <a href="javascript:void(0);" id="<?php echo $value["id"]; ?>" title="" class="smallButton action edit_field"  style="margin: 5px;">
            <img src="<?php echo $baseUrl; ?>images/icons/dark/pencil.png" alt=""/>
        </a>
        <a href="javascript:void(0);" id="<?php echo $value["id"]; ?>" title="" class="smallButton action delete_field" style="margin: 5px;">
            <img src="<?php echo $baseUrl; ?>images/icons/dark/close.png" alt=""/>
        </a>
    </td>
</tr>
<?php
}
?>