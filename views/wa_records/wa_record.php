<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string(), ['id' => 'wa_record_form']); ?>
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-3">
                                <?php 
                                    $selected_trip = [];
                                    foreach($trips as $key => $value) {
                                        $selected_trip[] = [
                                            'id' => $value['id'],
                                            'name' => str_pad($value['gr_number'], 4, '0', STR_PAD_LEFT) . ' - ' . strtoupper($value['vehicle_number'])
                                        ];
                                    }
                                    echo render_select('trip_id', 
                                        $selected_trip,
                                        ['id', 'name'],
                                        'trip',
                                        $wa_record->trip_id ?? '',
                                        ['required' => true, 'data-live-search' => true]
                                    ); 
                                ?>
                                <?php echo render_input('gr_number', '', $wa_record->gr_number ?? '', 'hidden'); ?>
                            </div>  
                            <div class="col-md-3">
                                <?php echo render_input('vehicle_no', 'Vehicle Number', $wa_record->vehicle_no ?? '', 'text', ['required' => true, 'class' => 'text-uppercase']); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('vehicle_owner', 'Vehicle Owner', $wa_record->vehicle_owner ?? '', 'text', ['required' => true]); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('driver_phone', 'Driver Phone', $wa_record->driver_phone ?? '', 'tel', ['maxlength' => 10, 'pattern' => '[0-9]{10}', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="in_datetime"><?php echo _l('IN Date/Time'); ?></label>
                                    <input type="datetime-local" name="in_datetime" id="in_datetime" class="form-control" value="<?php echo $wa_record->in_datetime ?? ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="out_datetime"><?php echo _l('OUT Date/Time'); ?></label>
                                    <input type="datetime-local" name="out_datetime" id="out_datetime" class="form-control" value="<?php echo $wa_record->out_datetime ?? ''; ?>">
                                </div>

                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="onplace_status"><?php echo _l('On Place Status'); ?></label>
                                    <select name="onplace_status" id="onplace_status" class="form-control">
                                        <option value="unloaded"><?php echo _l('Unloaded'); ?></option>
                                        <option value="loaded"><?php echo _l('Loaded'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="whatsapp_pod"><?php echo _l('Whatsapp POD'); ?></label>
                                    <select name="whatsapp_pod" id="whatsapp_pod" class="form-control">
                                        <option value="received"><?php echo _l('Received'); ?></option>
                                        <option value="not_received"><?php echo _l('Not Received'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('received_pod', 'Received POD', $wa_record->received_pod ?? '', 'text'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('paid_pod', 'Paid POD', $wa_record->paid_pod ?? '', 'text'); ?>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?> 
<script>
$(function() {
    appValidateForm($('form'), {
        trip_id: 'required',
        vehicle_owner: 'required',
        driver_phone: 'required',
        vehicle_no: 'required',
        in_datetime: 'required',
        out_datetime: 'required',
        onplace_status: 'required',
        whatsapp_pod: 'required',
        received_pod: 'required',
        paid_pod: 'required',
    });

    // Auto-populate addresses when consignor/consignee is selected
    $('#trip_id').on('change', function() {
        var tripId = $(this).val();
        if(tripId) {
            
            $.get(admin_url + 'tms/trips/get_trip_data/' + tripId, function(response) {
                var data = JSON.parse(response);
                if(data.success) {
                    $('#gr_number').val(data.data.gr_number);   
                    $('#vehicle_no').val(data.data.vehicle_number);
                    $('#vehicle_owner').val(data.data.vehicle_owner_name);
                    $('#driver_phone').val(data.data.driver_phone);
                }
            });
        }
    });
});
</script>
<style>
.select2-container .select2-selection--single {
    height: 36px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 34px;
}
</style>
