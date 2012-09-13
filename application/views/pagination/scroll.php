<div class="pagerholder" style="width: 600px;">
    <ul class="pages<?php if($class){echo ' '. $class; }?>" style="height: 30px;">
    <?php
    $Range = 8;

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

    for($i=$FirstViewPage; $i<=$LastViewPage; $i++)
    {
        ?>
        <li class="pageitem" style="width:28px;"><?php if($i!=$current_page) {?><a page="<?php echo $i; ?>" class="pagelink" href="<?php if($link){ echo sprintf($link, $i); }else{ echo 'javascript:void(0);';} ?>" ><?php echo $i; ?></a><?php } else { ?><?php echo '<div class="activepage">'.$i.'</div>'; ?><?php }?></li>
        <?php
    }
    ?>
    </ul>
    <div id="pagerslider<?php echo $class; ?>"></div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#pagerslider<?php echo $class; ?>').slider({
            min:1,
            max: <?php echo $last_page; ?>,
            step:1,
            value:<?php echo $current_page; ?>,
            stop:function(evt,obj)
            {
                location.href = "<?php echo $link?>".replace('%s', obj.value);
            },
            slide: function(evt, obj){
                Range = 8;
                FirstViewPage = obj.value-Range;
                LastViewPage = obj.value+Range;

                if(FirstViewPage<1)
                {
                    Dif = 1-FirstViewPage;
                    FirstViewPage = 1;
                    LastViewPage += Dif;
                }
                if(LastViewPage><?php echo $last_page; ?>-1)
                {
                    Dif = LastViewPage-(<?php echo $last_page; ?>-1);
                    FirstViewPage -= Dif;
                    LastViewPage = <?php echo $last_page; ?>-1;
                }
                if(FirstViewPage<1)
                {
                    FirstViewPage = 1;
                }
                var html = '';
                for(var i=FirstViewPage; i<=LastViewPage; i++)
                {
                    html += '<li class="pageitem" style="width:31px;">';
                    if(i==<?php echo $current_page; ?>)
                    {
                        html += '<div class="activepage">'+i+'</div>';
                    }
                    else
                    {
                        html += '<a page="'+i+'" class="pagelink" href="'+'<?php echo $link?>'.replace('%s', i)+'">'+i+'</a></li>';
                    }
                }
                $(".pages.<?php echo $class; ?>").html(html);
            }
        });
        setTimeout(function(){
            $('<a style="display:block; width:5px; left:'+($('.ui-slider-handle').css('left'))+'; background:red; height:8px; position:relative"></a>').appendTo("#pagerslider<?php echo $class; ?>");
            var append = '';
            if(<?php echo $current_page; ?>>1)
            {
                append = '<a onclick="location.href=\''+"<?php echo $link?>".replace('%s', <?php echo $current_page-1; ?>)+'\'" class="pagelink" page="<?php echo $current_page-1; ?>" style="position:relative; top:20px; right:25px;" href="'+"<?php echo $link?>".replace('%s', <?php echo $current_page-1; ?>)+'">&lt;prev</a>';
            }
            if(<?php echo $current_page; ?>!=<?php echo $last_page; ?>)
            {
                append += '<a onclick="location.href=\''+"<?php echo $link?>".replace('%s', <?php echo $current_page+1; ?>)+'\'"  class="pagelink" page="<?php echo $current_page+1; ?>" style="position:relative; top:20px; left:-20px;"href="'+"<?php echo $link?>".replace('%s', <?php echo $current_page+1; ?>)+'">next&gt;</a>';
            }
            $('.ui-slider-handle').html($('.ui-slider-handle').html()+append);
        }, 200);
    });
</script>