<script src="<?php echo $baseUrl; ?>js/form_validation.js"></script>
<!-- //******************************************************************* //-->
<!-- Right side -->
<div id="rightSide">
    <!-- Main content wrapper -->
    <div class="wrapper">
        <div class="widget">
            Your form was successfully submitted!
        </div>
    <div class="formRow">
        <label>XSS filter:<span class="req">*</span></label>
        <div class="formRight"><input type="text" value="<?php echo set_value('xssfilterValid'); ?>" class="validate[required]" name="xssfilterValid" id="xssfilterValid"/></div><div class="clear"></div>
        <noscript><?php  echo form_error('xssfilterValid'); ?></noscript>
    </div>
        <p><?php echo anchor('formvalidation', 'Try it again!'); ?></p>
    </div>
</div>
