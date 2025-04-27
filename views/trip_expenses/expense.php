<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string(), ['id' => 'trip_expense_form']); ?>
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
                                        $trip_expense->trip_id ?? '',
                                        ['required' => true, 'data-live-search' => true]
                                    ); 
                                ?>
                                <input type="hidden" name="gr_number" id="gr_number" value="<?php echo $trip_expense->gr_number ?? ''; ?>">
                            </div>  
                            
                            
                            <div class="col-md-3">
                                <?php echo render_input('type', 'Amount Type', $trip_expense->type ?? '', 'text'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('value', 'Amount', $trip_expense->value ?? '', 'number'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_date_input('expense_date', 'Expense Date', _d($trip_expense->expense_date ?? date('Y-m-d'))); ?>
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
        value: 'required',
        type: 'required',
        expense_date: 'required',
        
    });

    // Auto-populate addresses when consignor/consignee is selected
    $('#trip_id').on('change', function() {
        var tripId = $(this).val();
        if(tripId) {
            
            $.get(admin_url + 'tms/trips/get_trip_data/' + tripId, function(response) {
                var data = JSON.parse(response);
                if(data.success) {
                    $('#gr_number').val(data.data.gr_number);   
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
