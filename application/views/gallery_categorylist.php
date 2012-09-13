<div class="title">
    <img src="<?php echo $baseUrl; ?>images/icons/dark/images2.png" alt="" class="titleIcon">
    <a href="javascript:gallery.load_categories(1);"><h6>
    <?php    
    echo 'home';
    ?></h6></a>
    <?php 
    foreach($breadcrumbs as $v){
    ?>
    <a href="javascript:gallery.load_categories(<?php echo $v['node_id'];?>);"><h6>
    <?php
    echo '&gt;' . strip_tags($v['title']);
    ?></h6></a>
    <?php
    }
    ?>
    <?php if($current){ ?>
    <h6>&gt;<?php echo strip_tags($current[0]['title']); ?></h6>
<?php } ?>
</div>
<div class="gallery">
    <?php
    if($categories){?>
    <ul>
<?php foreach($categories as $k=>$v)
    {
?>
        <li id="<?php echo $v['id']; ?>" class="galleryitem droppablecat draggablecat" <?php if($v['coverid']){?>style="background: url(<?php echo $baseUrl.'uploads/'.$v['coverid'].'sm_'.  str_replace('.flv', '.jpg', $v['cover']) ?>)"<?php } ?>>
            <a href="javascript:gallery.load_categories(<?php echo $v['node_id'];?>)" title="" rel="lightbox">
                <div class="gallerytitle"><div class="gallerytitleback"><?php echo strip_tags($v['title']); ?></div></div>
            </a>
            <div class="actions" style="display: none; ">
                <a href="javascript:gallery.edit_gallery(<?php echo $v['id']?>);" title=""><img src="<?php echo $baseUrl; ?>images/icons/update.png" alt=""></a>
                <a id="<?php echo $v['id']; ?>" class="deletecategory" href="javascript:void(0);" title=""><img src="<?php echo $baseUrl; ?>images/icons/delete.png" alt=""></a>
            </div>
        </li>
<?php
    }
?>
        </ul>
    <?php
    }
    else
    {
        ?>
    This category has no sub categories
<?php }?>
<div class="fix"></div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".draggablecat").draggable({revert:true,revertDuration:0})
        $(".droppablecat").droppable(
                    {
                        accept:'.draggablecat',
                        drop: function(evt, obj) {
                            $.ajax({
                                url: gBaseUrl+'gallery/rearrangeitems',
                                data:{currentid:$(this).attr('id'), targettid:obj.draggable.attr('id')},
                                type:'POST',
                                success:function(){
                                    gallery.load_categories(gallery.parent_id);
                                }
                            });
                        }
                    }
        );
    });
</script>