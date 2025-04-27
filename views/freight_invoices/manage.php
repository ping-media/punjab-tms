<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-3">
                    <h4 class="tw-my-0 tw-font-bold tw-text-xl">Freight Invoices</h4>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('tms', '', 'create')) { ?>
                                <a href="<?php echo admin_url('tms/freight_invoices/invoice'); ?>" class="btn btn-primary pull-left display-block">
                                    <i class="fa-regular fa-plus tw-mr-1"></i>
                                    <?php echo _l('Create Freight Invoice'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php
                        $table_data = [
                            _l('Invoice #'),
                            _l('company'),
                            _l('amount'),
                            _l('phone'),
                            _l('status'),
                            _l('created_at'),
                            _l('due_date'),
                            _l('options')
                        ];
                        
                        render_datatable($table_data, 'freight_invoices');
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
    // initDataTable('.table-freight_invoices', admin_url + 'tms/freight_invoices/table', undefined, undefined, {}, [0, 'desc']);
    initDataTable('.table-freight_invoices', window.location.href, [], [], undefined, [0, 'desc']);
});
</script> 