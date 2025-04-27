<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string(), ['id' => 'freight_invoice_form']); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin"><?php echo $title; ?></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if(isset($freight_invoice) && $freight_invoice->id): ?>
                                    <a href="<?php echo admin_url('tms/freight_invoices/pdf/' . $freight_invoice->id); ?>?output_type=I" target="_blank" class="btn btn-default"><i class="fa-regular fa-file-pdf"></i> PDF</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php 
                                        $selected_consignor = [];
                                        foreach($consignors as $key => $value) {
                                            $selected_consignor[] = [
                                                'id' => $value['id'],
                                                'name' => $value['company']
                                            ];
                                        }
                                        echo render_select('consignor_id', 
                                            $selected_consignor,
                                            ['id', 'name'],
                                            'consignor',
                                            $freight_invoice->consignor_id ?? '',
                                            ['required' => true, 'data-live-search' => true]
                                        ); 
                                    ?>
                                </div>

                                <div>
                                <!-- <select id="clientid" name="clientid" data-live-search="true" data-width="100%"
                                    class="ajax-search<?= isset($invoice) && empty($invoice->clientid) ? ' customer-removed' : ''; ?>"
                                    data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                    <?php $selected = isset($invoice) ? $invoice->clientid : ($customer_id ?? ''); ?>
                                    <?php if ($selected != '') {
                                        $rel_data = get_relation_data('customer', $selected);
                                        $rel_val  = get_relation_values($rel_data, 'customer');
                                        echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                    } ?>
                                </select> -->
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- From State -->
                                        <?php   
                                            $states = [];
                                            foreach(get_indian_states() as $key => $value) {
                                                $states[] = [
                                                    'id' => $key,
                                                    'name' => $value
                                                ];
                                            }
                                            echo render_select('from_state', 
                                                $states,
                                                ['id', 'name'],
                                                'State',
                                                $freight_invoice->from_state ?? '',
                                                ['required' => true, 'data-live-search' => true]
                                            ); 
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('from_city', 'City', $freight_invoice->from_city ?? ''); ?>
                                    </div>
                                </div>
                                

                                <?php echo render_input('from_address', 'Address', $freight_invoice->from_address ?? ''); ?>
                                
                                <?php echo render_input('gst', 'GSTIN', $freight_invoice->gst ?? '', 'text', ['maxlength' => 15, 'minlength' => 15, 'pattern' => '[A-Za-z0-9]{10}', 'style' => 'text-transform: uppercase;']); ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="invoice_number"><?php echo _l('Invoice Number'); ?></label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <span id="prefix">INV-</span>
                                        </span>
                                        <?php $next_invoice_number = isset($next_invoice_number) ? $next_invoice_number : ''; ?>
                                        <input type="text" 
                                               name="invoice_number" 
                                               id="invoice_number" 
                                               class="form-control" 
                                               value="<?php echo isset($freight_invoice->invoice_number) && $freight_invoice->invoice_number > 0 ? str_pad($freight_invoice->invoice_number, 4, '0', STR_PAD_LEFT) : $next_invoice_number; ?>" 
                                               data-unique="true">
                                    </div>
                                    <div id="invoice_number_validation_message"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $invoice_date = (isset($freight_invoice) ? _d($freight_invoice->invoice_date) : _d(date('Y-m-d'))); ?>
                                        <?php echo render_date_input('invoice_date', 'invoice_date', $invoice_date); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $due_date = (isset($freight_invoice) ? _d($freight_invoice->due_date) : _d(date('Y-m-d'))); ?>
                                        <?php echo render_date_input('due_date', 'due_date', $due_date); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('sac_code', 'SAC Code', $freight_invoice->sac_code ?? ''); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('sac_description', 'SAC Description', $freight_invoice->sac_description ?? ''); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('to_city', 'Place of Supply', $freight_invoice->to_city ?? ''); ?>
                                    </div>
                                    <div class="col-md-6">
                                       <!-- From State -->
                                       <?php   
                                            $states = [];
                                            foreach(get_indian_states() as $key => $value) {
                                                $states[] = [
                                                    'id' => $key,
                                                    'name' => $value
                                                ];
                                            }
                                            echo render_select('to_state', 
                                                $states,
                                                ['id', 'name'],
                                                'State',
                                                $freight_invoice->to_state ?? '',
                                                ['required' => true, 'data-live-search' => true]
                                            ); 
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="status" class="control-label"><?php echo _l('status'); ?></label>
                                            <?php $status = $freight_invoice->status ?? 'unpaid'; ?>
                                            <select class="selectpicker display-block" name="status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value="unpaid" <?php echo ($status == 'unpaid') ? 'selected' : ''; ?>><?php echo _l('Unpaid'); ?></option>    
                                                <option value="draft" <?php echo ($status == 'draft') ? 'selected' : ''; ?>><?php echo _l('Draft'); ?></option>
                                                <option value="paid" <?php echo ($status == 'paid') ? 'selected' : ''; ?>><?php echo _l('Paid'); ?></option>
                                                <option value="cancelled" <?php echo ($status == 'cancelled') ? 'selected' : ''; ?>><?php echo _l('Cancelled'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo render_input('total_amount', 'Total Amount', $freight_invoice->total_amount ?? '', 'number', ['readonly' => true]); ?>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        <?php $value = (isset($invoice) ? $invoice->status : 'draft'); ?>
                        
                        <div class="items">
                            <div class="row">
                                
                                <div class="col-md-4">
                                    <div class="input-group input-group-select">
                                        <div class="items-select-wrapper">
                                            <select name="trips"
                                                class="selectpicker no-margin _select_input_group"
                                                data-width="false" id="trip_select"
                                                data-none-selected-text="<?= _l('add_item'); ?>"
                                                data-live-search="true"
                                                data-consignor-id="">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                        <?php if (staff_can('create', 'items')) { ?>
                                        <div class="input-group-btn">
                                            <a href="javascript:void(0)" class="btn btn-default" id="add-trip">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                        <?php } ?>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table items table-main-invoice-edit">
                                            <thead>
                                                <tr>
                                                    <th class="text-left"><?php echo _l('id'); ?></th>
                                                    <th class="text-left"><?php echo _l('Vehicle No.'); ?></th>
                                                    <th class="text-left"><?php echo _l('Date'); ?></th>
                                                    <th class="text-left"><?php echo _l('GR. No.'); ?></th>
                                                    <th class="text-left">Party Invoice</th>
                                                    <th class="text-left"><?php echo _l('From'); ?></th>
                                                    <th class="text-left"><?php echo _l('Destination'); ?></th>
                                                    <th class="text-left"><?php echo _l('Material'); ?></th>
                                                    <th class="text-left"><?php echo _l('Pcs'); ?></th>
                                                    <!-- <th class="text-left"><?php echo _l('Rate'); ?></th>
                                                    <th class="text-left"><?php echo _l('Detention L&U Point'); ?></th> -->
                                                    <!-- <th class="text-left"><?php echo _l('TDS'); ?></th> -->
                                                    <th class="text-left"><?php echo _l('Amount'); ?></th>
                                                    <th class="text-left"><?php echo _l('Action'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               <?php 
                                               if(isset($freight_invoice->trips) && $freight_invoice->trips != '') {
                                                    $trip_ids = $freight_invoice->trips;
                                                    $trip_ids_array = array_column($trip_ids, 'id');
                                                    $trip_ids_string = implode(',', $trip_ids_array);
                                                    echo '<input type="hidden" name="trips" id="trip_ids" value="'.$trip_ids_string.'">';
                                                    foreach($trip_ids as $key => $trip) {
                                                        echo '<tr data-trip-id="'.$trip['id'].'">';
                                                        echo '<td>' . (isset($key) ? intval($key) + 1 : 1) . '</td>';
                                                        echo '<td>'.$trip['vehicle_number'].'</td>';
                                                        echo '<td>'.$trip['start_date'].'</td>';
                                                        echo '<td>'.$trip['gr_number'].'</td>';
                                                        echo '<td>'.$trip['gr_number'].'</td>';
                                                        echo '<td>'.$trip['from_city'].'</td>';
                                                        echo '<td>'.$trip['to_city'].'</td>';
                                                        echo '<td></td>';
                                                        echo '<td></td>';
                                                        // echo '<td>'.$trip['to_city'].'</td>';
                                                        // echo '<td>'.$trip['to_city'].'</td>';
                                                        echo '<td class="amount">'.number_format($trip['total_freight'], 2).'</td>';
                                                        echo '<td>
                                                                <button type="button" class="btn btn-danger btn-xs remove-item">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </td>';
                                                    }
                                                    
                                               }else{
                                                echo '<input type="hidden" name="trips" id="trip_ids" value="">';
                                               }
                                               ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="total-row">
                                                    <td colspan="9" class="text-right"><strong>Total Amount:</strong></td>
                                                    <td id="total_amount_display" class="text-left"><?php echo number_format($freight_invoice->total_amount ?? 0, 2); ?></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8 col-md-offset-4">
                                
                            </div>
                        </div>
                        
                        
                        
                        <div class="btn-bottom-toolbar text-right">
                            <!-- <?php if(isset($freight_invoice) && $freight_invoice->id): ?>
                                <a href="<?php echo admin_url('tms/freight_invoices/pdf/' . $freight_invoice->id); ?>?output_type=I" target="_blank" class="btn btn-default"><i class="fa-regular fa-file-pdf"></i> PDF</a>
                            
                            <?php endif; ?> -->
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                        
                        
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<style>
    .dropdown.bootstrap-select{
        width: 100% !important;
    }
    
    .total-row {
        background-color: #f9f9f9;
        font-weight: bold;
    }
    
    .total-row td {
        border-top: 2px solid #ddd !important;
    }
</style>
<script>
$(function() {
    appValidateForm($('#freight_invoice_form'), {
        invoice_number: 'required',
        trip_reference: 'required',
        invoice_date: 'required',
        due_date: 'required',
        status: 'required',
        consignor_id: 'required',
        from_state: 'required',
        from_address: 'required',
        from_city: 'required',
        to_state: 'required',
        to_city: 'required',
    });

    // Calculate and update total amount
    function updateTotalAmount() {
        let total = 0;
        $('.items tbody tr').each(function() {
            let amountText = $(this).find('td.amount').text() || '0';
            // Remove commas and convert to number
            let amount = parseFloat(amountText.replace(/,/g, '')) || 0;
            total += amount;
        });
        
        // Update the total display
        $('#total_amount_display').text(total.toLocaleString('en-IN', {maximumFractionDigits: 2, minimumFractionDigits: 2}));
        
        // Update the hidden total amount field
        $('#total_amount').val(total.toFixed(2));
    }

    // Initial calculation on page load
    // updateTotalAmount();

    // alert($('#consignor_id').val());

    // Trigger change event if consignor_id has a pre-selected value
    if ($('#consignor_id').val()) {
        setTimeout(function() {
            $('#consignor_id').trigger('change');
        }, 1000);
    }

    // Auto-populate addresses when consignor/consignee is selected
    $('#consignor_id').on('change', function() {
        var consignorId = $(this).val();
        if(consignorId) {
            $.get(admin_url + 'tms/consignors/get/' + consignorId, function(response) {
                var data = JSON.parse(response);
                if(data.success) {
                    if(data.city && data.city != '') {
                        $('#from_city').val(data.city);
                        $('#from_city').parent('.form-group').removeClass('has-error');
                    }
                    if(data.address && data.address != '') {
                        $('#from_address').val(data.address);
                        $('#from_address').attr('aria-invalid', false);
                    }
                    if(data.gst && data.gst != '') {
                        $('#gst').val(data.gst);
                        $('#gst').attr('aria-invalid', false);
                    }
                    $('#from_state').val(data.state).trigger('change');

                    let trips = data.trips || [];
                    let options = '<option value=""></option>';
                    trips.forEach(function(trip) {
                        options += '<option value="' + trip.id + '">GR-' + ('0000' + trip.gr_number).slice(-4) + ' - ' + moment(trip.start_date).format('DD/YY/MM') + '</option>';
                    });
                    $('#trip_select').html(options).selectpicker('refresh');
                }
            });
        }
    });

    $('#invoice_number').on('blur', function() {
        var gr_number = $(this).val();
        $.ajax({
            url: admin_url + 'tms/trips/check_invoice_number',
            type: 'POST',
            data: {gr_number: gr_number},
            success: function(response) {
                var data = JSON.parse(response);
                if(!data.unique) {
                    $('#invoice_number_validation_message').html('<span class="text-danger">This invoice number exists</span>');
                    
                } else {
                    $('#invoice_number_validation_message').html('');
                }
            }
        });
    });

    // Add item row
    $('#add-trip').on('click', function() {
        let trip_id = $('#trip_select').val();
        // Get trip data via AJAX
        $.ajax({
            url: admin_url + 'tms/trips/get_trip_data/' + trip_id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    let trip = response.data;
                    
                    // Check if trip already exists in table
                    let existingRow = $(`.items tbody tr[data-trip-id="${trip_id}"]`);
                    if(existingRow.length > 0) {
                        alert_float('warning', 'This trip is already added to the invoice');
                        return;
                    }

                    // Get existing trip IDs and append new one
                    let existingIds = $('#trip_ids').val();
                    let tripIdsArray = existingIds ? existingIds.split(',') : [];
                    tripIdsArray.push(trip_id);
                    $('#trip_ids').val(tripIdsArray.join(','));

                    let items = trip.items || [];
                    let material = items && items.length > 0 ? items[0].name : '';
                    let pcs = items && items.length > 0 ? items[0].quantity : '';

                    // Update row with trip data
                    let rowCount = $('.items tbody tr').length + 1;
                    let newRow = `
                        <tr class="item text-capitalize" data-trip-id="${trip_id}">
                            <td>${rowCount}</td>
                            <td class="text-uppercase">${trip.vehicle_number}</td>
                            <td>${trip.start_date}</td>
                            <td>${trip.gr_number ? trip.gr_number : ''}</td>
                            <td>${trip.gr_number}</td>
                            <td>${trip.from_city}</td>
                            <td>${trip.to_city}</td>
                            <td>${material}</td>
                            <td>${pcs}</td>
                            <td class="amount">${parseFloat(trip.total_freight).toLocaleString('en-IN', {maximumFractionDigits: 2, minimumFractionDigits: 2})}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-xs remove-item">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('.items tbody').append(newRow);
                    updateTotalAmount();
                } else {
                    alert_float('danger', 'Error getting trip data');
                }
            },
            error: function() {
                alert_float('danger', 'Error getting trip data');
            }
        });
    });

    // remove item 
    $(document).on('click', '.remove-item', function() {
        let row = $(this).closest('tr');
        let tripId = row.data('trip-id');
        let tripIdsArray = $('#trip_ids').val().split(',');
        tripIdsArray = tripIdsArray.filter(id => id !== tripId.toString()); // Convert tripId to string for comparison
        
        // Remove empty values and update hidden input
        tripIdsArray = tripIdsArray.filter(id => id !== '');
        $('#trip_ids').val(tripIdsArray.join(','));
    
        row.remove();
        updateTotalAmount();
    });

    // Add trip event - update total after adding new item
    // $('#add-trip').on('click', function() {
    //     setTimeout(updateTotalAmount, 1000); // Update after AJAX call completes
    // });
});
</script> 