<ul class="pages<?php if($class){echo ' '. $class; }?>">
    <li class="pageitem">
        <?php
        $Range = 4;

        $FirstViewPage = $current_page-$Range;
        $LastViewPage = $current_page+$Range;

        if($FirstViewPage<1)
        {
            $Dif = 1-$FirstViewPage;
            $FirstViewPage = 1;
            $LastViewPage += $Dif;
        }
        if($LastViewPage>$last_page)
        {
            $Dif = $LastViewPage-($last_page);
            $FirstViewPage -= $Dif;
            $LastViewPage = $last_page;
        }
        if($FirstViewPage<1)
        {
            $FirstViewPage = 1;
        }
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
    <?php
    for($i=$FirstViewPage; $i<=$LastViewPage; $i++)
    {
        ?>
    <li class="pageitem"><?php if($i!=$current_page) {?><a page="<?php echo $current_page+1; ?>" class="pagelink" href="<?php if($link){ echo sprintf($link, $i); }else{ echo 'javascript:void(0);';} ?>" ><?php echo $i; ?></a><?php } else { ?><?php echo '<div class="activepage">'.$i.'</div>'; ?><?php }?></li>
        <?php
    }
    ?>
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