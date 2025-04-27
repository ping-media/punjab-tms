<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-3">
                    <h4 class="tw-my-0 tw-font-bold tw-text-xl">Vehicle Owners</h4>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('tms', '', 'create')) { ?>
                                <a href="<?php echo admin_url('tms/vehicle_owners/vehicle_owner'); ?>" class="btn btn-primary pull-left display-block">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_vehicle_owner'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php
                        $table_data = [
                            '#',
                            _l('name'),
                            _l('email'),
                            _l('phone'),
                            _l('address'),
                            _l('city'),
                            _l('state'),
                            _l('status'),
                            _l('created_at'),
                            // _l('options')
                        ];
                        render_datatable($table_data, 'vehicle_owners');
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
    initDataTable('.table-vehicle_owners', window.location.href, [], [], undefined, [0, 'desc']);

    // Handle status toggle
    $('body').on('change', '.status-switch', function() {
        var switchElement = $(this);
        var id = switchElement.data('id');
        var url = switchElement.data('switch-url');
        var status = switchElement.prop('checked') ? 1 : 0;

        $.post(url, {
            id: id,
            status: status
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
            } else {
                alert_float('warning', response.message);
                // Revert switch if update failed
                switchElement.prop('checked', !status);
            }
        }).fail(function() {
            alert_float('danger', 'Error updating status');
            // Revert switch if request failed
            switchElement.prop('checked', !status);
        });
    });
});
</script> 