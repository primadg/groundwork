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
        if($current_page == 1)
        {
        ?><div class="activepage">1</div><?php
        }
        else
        { ?>
        <a page="1" class="pagelink" href="<?php if($link){ echo sprintf($link, 1); }else{ echo 'javascript:void(0);';} ?>" >1</a>
        <?php
        }
        ?>
    </li>
    <?php
    $Range = 3;
    
    $FirstViewPage = $current_page-$Range;
    $LastViewPage = $current_page+$Range;

    if($FirstViewPage<2)
    {
        $Dif = 2-$FirstViewPage;
        $FirstViewPage = 2;
        $LastViewPage += $Dif;
    }
    if($LastViewPage>$last_page-1)
    {
        $Dif = $LastViewPage-($last_page-1);
        $FirstViewPage -= $Dif;
        $LastViewPage = $last_page-1;
    }
    if($FirstViewPage<2)
    {
        $FirstViewPage = 2;
    }
    if($FirstViewPage-1>1)
    {
        echo '<li>...</li>';
    }
    
    for($i=$FirstViewPage; $i<=$LastViewPage; $i++)
    {
        ?>
    <li class="pageitem"><?php if($i!=$current_page) {?><a page="<?php echo $i; ?>" class="pagelink" href="<?php if($link){ echo sprintf($link, $i); }else{ echo 'javascript:void(0);';} ?>" ><?php echo $i; ?></a><?php } else { ?><?php echo '<div class="activepage">'.$i.'</div>'; ?><?php }?></li>
        <?php
    }
    if($LastViewPage<$last_page-1)
    {
        echo '<li>...</li>';
    }
    ?>
    <li>
        <?php
        if($current_page == $last_page)
        {
        ?><?php echo '<div class="activepage">'.$i.'</div>';?><?php
        }
        else
        {
            ?><a page="<?php echo $last_page; ?>" class="pagelink" href="<?php if($link){ echo sprintf($link, $last_page); }else{ echo 'javascript:void(0);';} ?>" ><?php echo $last_page;?></a>
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