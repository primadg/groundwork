<link rel="stylesheet" href="<?php echo $baseUrl; ?>js/plugins/fileupload/css/jquery.fileupload-ui.css">
<script src="<?php echo $baseUrl; ?>js/plugins/fileupload/js/jquery.iframe-transport.js"></script>
<script src="<?php echo $baseUrl; ?>js/plugins/fileupload/js/jquery.fileupload.js"></script>
<script src="<?php echo $baseUrl; ?>js/plugins/fileupload/js/jquery.fileupload-ui.js"></script>
<script type="text/javascript" src="<?php echo $baseUrl; ?>js/gallery.js"></script>
<script type="text/javascript">
gallery.parent_id = <?php echo $parent; ?>;
</script>
<div id="rightSide">
    <!-- Main content wrapper -->
    <div class="wrapper">
        <!-- Images gallery -->
        <div class="widget" id="categorylist">
            
        </div>
        <div>
            <a title="Add category" href="javascript:void(0);" class="addicon addcategory"></a>
        </div>
        <div class="widget" id="filelist">
        </div>
        <div class="widget" id="filelistempty">
        </div>
        <div>
            <a title="Add file" href="javascript:void(0);" class="addicon addfile"></a>
        </div>
        <div>
    </div>
    <!-- Footer line -->
    <div id="footer">
        <div class="wrapper"></div>
    </div>
</div>
<div id="dialog">

</div>