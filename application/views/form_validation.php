<script src="<?php echo $baseUrl; ?>js/form_validation.js"></script>
<!-- //******************************************************************* //-->
<!-- Right side -->
<div id="rightSide">
    <!-- Main content wrapper -->
    <div class="wrapper">
        <!-- Validation form -->
        <form id="validate" class="form" action="" method="POST" enctype="multipart/form-data">
        	<fieldset>
                <div class="widget">
                    <div class="title"><img src="images/icons/dark/alert.png" alt="" class="titleIcon" /><h6>Form validation</h6></div>
                    <div class="formRow">
                        <label>Email address:<span class="req">*</span></label>
                        <div class="formRight"><input type="text" value="<?php echo set_value('emailValid'); ?>" class="validate[required,custom[email]]" name="emailValid" id="emailValid"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('emailValid'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>Minimum lenght (6):<span class="req">*</span></label>
                        <div class="formRight"><input type="text" value="<?php echo set_value('minValid'); ?>" class="validate[required,minSize[6]]" name="minValid" id="minValid"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('minValid'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>Maximum lenght (6):<span class="req">*</span></label>
                        <div class="formRight"><input type="text" class="validate[required,maxSize[6]]" value="<?php echo set_value('maxValid'); ?>" name="maxValid" id="maxValid"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('maxValid'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>Minimum value (5):<span class="req">*</span></label>
                        <div class="formRight"><input type="text" class="validate[required,min[5],custom[onlyNumberSp]]" value="<?php echo set_value('min'); ?>" name="min" id="min"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('min'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>Maximum value (10):<span class="req">*</span></label>
                        <div class="formRight"><input type="text" class="validate[required,max[10],custom[onlyNumberSp]]" value="<?php echo set_value('max'); ?>" name="max" id="max"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('max'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>Only numbers:<span class="req">*</span></label>
                        <div class="formRight"><input type="text" value="<?php echo set_value('numsValid'); ?>" class="validate[required,custom[number]]" name="numsValid" id="numsValid"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('numsValid'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>Date:<span class="req">*</span></label>
                        <div class="formRight"><input type="text" value="<?php echo set_value('dateValid'); ?>" class="validate[required,custom[date]]" name="dateValid" id="dateValid"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('dateValid'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>Date and Time:<span class="req">*</span></label>
                        <div class="formRight"><input type="text" value="<?php echo set_value('datetimeValid'); ?>" class="validate[required,custom[datetime]]" name="datetimeValid" id="datetimeValid"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('datetimeValid'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>Time:<span class="req">*</span></label>
                        <div class="formRight"><input type="text" value="<?php echo set_value('timeValid'); ?>" class="validate[required,custom[time]]" name="timeValid" id="timeValid"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('timeValid'); ?></noscript>
                    </div>
                    <div class="formRow">
                        <label>XSS filter:<span class="req">*</span></label>
                        <div class="formRight"><input type="text" value="<?php echo set_value('xssfilterValid'); ?>" class="validate[required]" name="xssfilterValid" id="xssfilterValid"/></div><div class="clear"></div>
                        <noscript><?php  echo form_error('xssfilterValid'); ?></noscript>
                    </div>
                    <div class="formSubmit"><input type="submit" value="submit" class="redB" /></div>
                    <div class="clear"></div>
                </div>

            </fieldset>
        </form>
    </div>
</div>
