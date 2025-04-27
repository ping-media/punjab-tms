<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-3">
                    <h4 class="tw-my-0 tw-font-bold tw-text-xl"><?php echo _l('trip_expenses'); ?></h4>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('tms', '', 'create')) { ?>
                                <a href="<?php echo admin_url('tms/trip_expenses/expense'); ?>" class="btn btn-primary pull-left display-block">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_trip_expense'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php
                        $table_data = [
                            '#',
                            'GR Number',
                            'Type',
                            'Value',
                            'Expense Date',
                            'Created At',
                        ];
                        render_datatable($table_data, 'trip_expenses');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-trip_expenses', window.location.href, [], [], undefined, [0, 'desc']);
});
</script> 