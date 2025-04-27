<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-3">
                    <h4 class="tw-my-0 tw-font-bold tw-text-xl">Whatsapp Records</h4>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('tms', '', 'create')) { ?>
                                <a href="<?php echo admin_url('tms/wa_records/wa_record'); ?>" class="btn btn-primary pull-left display-block">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_wa_record'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php
                        $table_data = [
                            '#',
                            _l('gr_number'),
                            _l('vehicle_no'),
                            _l('vehicle_owner'),
                            _l('driver_phone'),
                            _l('in_datetime'),
                            _l('out_datetime'),
                            _l('onplace_status'),
                            _l('whatsapp_pod'),
                            _l('received_pod'),
                            _l('paid_pod'),
                            _l('created_at'),
                            _l('options')
                        ];
                        render_datatable($table_data, 'wa_records');
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
    initDataTable('.table-wa_records', window.location.href, [], [], undefined, [0, 'desc']);
});
</script> 