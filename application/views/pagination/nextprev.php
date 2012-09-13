<ul class="pages<?php if($class){echo ' '. $class; }?>">
    <li class="pageitem">
        <?php
        if($current_page == 1)
        {
        ?>Previous<?php
        }
        else
        { ?>
        <a page="<?php echo $current_page-1; ?>" class="pagelink" href="<?php if($link){ echo sprintf($link, $current_page-1); }else{ echo 'javascript:void(0);';} ?>" >Previous</a>
        <?php 
        }
        ?>
    </li>
    <li>
    <?php
    if($current_page == $last_page)
    {
    ?>Next<?php 
    }
    else
    {
        ?><a page="<?php echo $current_page+1; ?>" class="pagelink" href="<?php if($link){ echo sprintf($link, $current_page+1); }else{ echo 'javascript:void(0);';} ?>" >Next</a>
        <?php
    }
    ?>
    </li>
</ul>