<div>
    <embed type="application/x-shockwave-flash" src="http:<?php echo $baseUrl.'images/player.swf'?>" width="540" height="390" style="undefined" id="mpl" name="mpl" quality="high" allowfullscreen="true" allowscriptaccess="always" wmode="opaque" flashvars="file=http:<?php echo $baseUrl.'uploads/'.$filedata['id'].'or_'.$filedata['file'];?>&amp;autostart=false" />
    <div><b>Description:</b><?php echo strip_tags($filedata['description']); ?></div>
</div>

