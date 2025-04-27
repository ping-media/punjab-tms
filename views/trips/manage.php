<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-3">
                    <h4 class="tw-my-0 tw-font-bold tw-text-xl">Trips</h4>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('tms', '', 'create')) { ?>
                                <a href="<?php echo admin_url('tms/trips/trip'); ?>" class="btn btn-primary pull-left display-block">
                                    <i class="fa-regular fa-plus tw-mr-1"></i>
                                    <?php echo _l('new_trip'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php
                        $table_data = [
                            _l('GR Number'),
                            _l('consignor'),
                            _l('driver'),
                            _l('trip_type'),
                            _l('From'),
                            _l('To'),
                            _l('Status'),
                            _l('freight_amount'),
                            _l('created_at'),
                            // _l('options')
                        ];
                        
                        render_datatable($table_data, 'trips');
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
    // var table = initDataTable('.table-trips', admin_url + 'tms/trips', undefined, undefined, {}, [0, 'desc']);
    initDataTable('.table-trips', window.location.href, [], [], undefined, [0, 'desc']);
});
</script> 