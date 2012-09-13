<script src="<?php echo $baseUrl; ?>js/liststaticajax.js"></script>
<!-- //******************************************************************* //-->
<div id="dialog-modal" title="Basic modal dialog">
	<div id="content-dialog-modal">
        <form>
            <div class="clear">
                <label for="edit_name">Edit Name</label>
                <input type="text" name="edit_name" id="edit_name" class="text ui-widget-content ui-corner-all autocomplete-off" />
            </div>
            <div class="clear">
                <label for="edit_date_of_birth">Edit Date of Birth</label>
                <input type="text" name="edit_date_of_birth" id="edit_date_of_birth" class="text ui-widget-content ui-corner-all autocomplete-off datepicker" />
            </div>
            <div class="clear">
                <label for="edit_phone">Edit Phone</label>
                <input type="text" name="edit_phone" id="edit_phone" class="text ui-widget-content ui-corner-all autocomplete-off" />
            </div>
            <div class="clear">
                <label for="edit_email">Edit Email</label>
                <input type="text" name="edit_email" id="edit_email" class="text ui-widget-content ui-corner-all autocomplete-off" />
            </div>
        </form>
    </div>
</div>
<!-- //******************************************************************* //-->
<!-- //******************************************************************* //-->
<div id="rightSide">
    <div id="top_pagination">
        <?php echo $pagination; ?>
    </div>
    <div class="wrapper">
    <!-- //******************************************************************* //-->
    <!-- Table with check all checkboxes fubction -->
        <div class="widget">
        <div class="title">
            
            <a id="top_delete_check_fields" href="javascript:void(0);" title="" class="button basic">
                <span>Delete fields</span>
            </a>
            
            <span class="titleIcon">
                <div class="checker" id="uniform-titleCheck"><span class=""><input type="checkbox" id="titleCheck" name="titleCheck" style="opacity: 0; "></span></div>
            </span>
        </div>
            <div class="ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix">
                <div class="dataTables_filter_sTable">
                    <label><span class="itemsPerPage">Search:</span>
                        <input type="text" placeholder="type here..." id="search_in_table"/>
                        <div class="srch"></div>
                    </label> 
                </div>
                <div class="dataTables_length_sTable">
                    <label>
                        <span class="itemsPerPage">Items per page:</span>
                        <div class="selector" id="uniform-tableFilter">
                            <span>10</span>
                            <select name="tableFilter" id="tableFilter" style="opacity: 0; ">
                                <option value="10" selected="selected">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </label>
                </div>
            </div>
          <table cellpadding="0" cellspacing="0" width="100%" class="titleIcon sTable withCheck" id="checkAll">
              <thead>
                  <tr> 
                      <td><img src="<?php echo $baseUrl; ?>images/icons/tableArrows.png" alt="" /></td>
                      <td class="sortCol" field="id"><div>ID&nbsp;&nbsp;&nbsp;&nbsp;<span></span></div></td>
                      <td class="sortCol" field="name"><div>Name<span></span></div></td>
                      <td class="sortCol" field="date_of_birth"><div>Date of Birth<span></span></div></td>
                      <td class="sortCol" field="phone"><div>Phone<span></span></div></td>
                      <td class="sortCol" field="email"><div>E-mail<span></span></div></td>
                      <td><div>Actions</div></td>
                  </tr>
              </thead>
              <tbody>
                  <?php
                    // Вызываем отображения наших данных
                    // чтобы мы правили только в одном файле
                    // и могли использовать для подгрузки с помощью AJAX
                    $this->load->view('block_list_table'); 
                  ?>
              </tbody>
              <tfoot>
                  <tr>
                      <td colspan="7">
                          <a id="bottom_delete_check_fields" href="javascript:void(0);" title="" class="button basic">
                             <span>Delete fields</span>
                          </a>
                          <?php
                          // Если у нас отсутствуют данные в БД
                          
                          ?>
                          <strong id="empty_database">
                          <?php
                          if(isset($emptyDatabase)&&!empty($emptyDatabase))
                          {
                              echo $emptyDatabase;
                          }
                          ?>
                          </strong>
                          <strong id="total_count_fields">
                              <?php
                              if(!isset($emptyDatabase) || empty($emptyDatabase))
                              {
                                if(isset($lastNumber)&&!empty($lastNumber)&&isset($firstNumber)&&!empty($firstNumber)&&isset($totalCountInDB)&&!empty($totalCountInDB))
                                {
                                    echo $firstNumber." - ".$lastNumber." / ".$totalCountInDB;
                                }
                              }
                              ?>
                          </strong>
                      </td>
                  </tr>
              </tfoot>
          </table>
          
        </div>
    <!-- //******************************************************************* //-->
    </div>
    <div id="bottom_pagination">
        <?php echo $pagination; ?>
    </div>
</div>