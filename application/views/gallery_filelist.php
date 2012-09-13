<div class="title">
    <span class="titleIcon">
        
                <?php if($parent){ ?><input id="filelistall" type="checkbox" id="titleCheck" name="titleCheck"><?php } ?>
            
    </span>
    <?php if($parent){ ?><h6>Files in categories<label><input id="direct_flag" <?php if($direct){echo 'checked="checked"';}?> type="checkbox" />Only direct</label></h6>
    <?php
    }
    else
    {
        ?>
        <h6>Files without category</h6>
        <?php
    }
    ?>
</div>
<table cellpadding="0" cellspacing="0" width="100%" class="sTable withCheck mTable" id="checkAll">
    <?php if($parent){ ?>
    <thead>
        <tr>
            <td><img src="<?php echo $baseUrl; ?>images/icons/tableArrows.png" alt=""></td>
            <td>Image</td>
            <td class="sortCol <?php
                    if($field == 'title')
                    {
                        if(strtoupper($dir) == 'ASC')
                        {
                            echo 'headerSortDown';
                        }
                        else
                        {
                            echo 'headerSortUp';
                        }
                    }
                    else
                    {
                        echo 'header';
                    }?>"><div class="sorter" sort="title">Title<span></span></div></td>
            <td class="sortCol <?php
                    if($field == 'created')
                    {
                        if(strtoupper($dir) == 'ASC')
                        {
                            echo 'headerSortDown';
                        }
                        else
                        {
                            echo 'headerSortUp';
                        }
                    }
                    else
                    {
                        echo 'header';
                    }?>"><div class="sorter" sort="created">Date<span></span></div></td>
            <td>File info</td>
            <td>Actions</td>
        </tr>
    </thead>
    <?php } ?>
    
    <tfoot>
        <tr>
            <td colspan="6">
                <?php if(!$parent){ ?>
                <div class="itemActions">
                    <label>Apply action:</label>
                    <div class="selector" id="uniform-undefined"><span>Select action...</span>
                        <select style="opacity: 0; " id="actionset">
                            <option value="">Select action...</option>
                            <option value="Delete">Delete</option>
                            <option value="Move">Move somewhere</option>
                        </select>
                    </div>
                </div>
                <?php } ?>
                <?php echo $paginator; ?>
            </td>
        </tr>
    </tfoot>
    
    <tbody>
        <?php if(count($files))
        {
        ?>
        <?php foreach($files as $v){?>
        <tr style="cursor: move;" id="<?php echo $v['id']; ?>" class="<?php if($parent){ ?>draggablefile droppablefile<?php } else{?>draggableefile droppableefile<?php }?>">
            <td width="37px"><input type="checkbox" value="<?php echo $v['id']; ?>" class="filelistchecker" name="checkRow" /></td>
            <td align="center" style="border-left-color: rgb(203, 203, 203); ">
                <a href="javascript:gallery.showfile(<?php echo $v['id']; ?>);" title="" class="showfile">
                    <img src="<?php if($v['type'] == GALERYITEM_TYPE_VIDEO){$v['file'] = str_replace(pathinfo($v['file'],PATHINFO_EXTENSION), 'jpg', $v['file']);} echo $baseUrl; ?>uploads/<?php echo $v['id'] .'th_'.$v['file'];?>" alt="">
                </a>
            </td>
            <td width="142px"><a href="javascript:gallery.showfile(<?php echo $v['id']; ?>);" title=""><?php echo strip_tags($v['title']); ?></a></td>
            <td align="center"><?php echo date('M d, Y H:i', strtotime($v['created'])); ?></td>
            <td class="fileInfo"><span><strong>Size:</strong> <?php echo $v['size']; ?> Kb</span><span><strong>Format:</strong> .<?php echo $v['format'];?></span></td>
            <td class="actBtns">
                <a href="#" class="tipS update" id="<?php echo $v['id']; ?>" original-title="Update">
                    <img src="<?php echo $baseUrl; ?>images/icons/edit.png" alt="">
                </a>
                <a href="#" class="tipS delete" id="<?php echo $v['id']; ?>" original-title="Remove">
                    <img src="<?php echo $baseUrl; ?>images/icons/remove.png" alt="">
                </a>
            </td>
        </tr>
        <?php } ?>
        <?php
        }
        else
        {
            ?>
            <tr>
                <td colspan="6" align="center">There is no files</td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        if(gallery.only_direct)
        {
            $(".draggablefile").draggable({revert:true,revertDuration:0})
            $(".droppablefile").droppable(
            {
                accept:'.draggablefile',
                drop: function(evt, obj) {
                    $.ajax({
                        url: gBaseUrl+'gallery/rearrangeitems',
                        data:{currentid:$(this).attr('id'), targettid:obj.draggable.attr('id')},
                        type:'POST',
                        success:function(){
                            gallery.order = 'numleft';
                            gallery.load_categories(gallery.parent_id);
                        }
                    });
                }
            }
            );
        }
        $(".draggableefile").draggable({revert:true,revertDuration:0})
        $(".droppableefile").droppable(
        {
            accept:'.draggableefile',
            drop: function(evt, obj) {
                $.ajax({
                    url: gBaseUrl+'gallery/rearrangeitems',
                    data:{currentid:$(this).attr('id'), targettid:obj.draggable.attr('id')},
                    type:'POST',
                    success:function(){
                        gallery.order = 'numleft';
                        gallery.load_categories(gallery.parent_id);
                    }
                });
            }
        }
        );
    });
</script>