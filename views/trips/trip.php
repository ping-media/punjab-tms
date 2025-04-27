<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'trip-form']); ?>
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin"><?php echo $title; ?></h4>
                            </div>
                            <div class="col-md-2">
                                <?php if(isset($trip->consignor_id) && $trip->consignor_id): ?>
                                    <a href="<?php echo admin_url('tms/trips/pdf/' . $trip->id ); ?>?output_type=I&type=consignor" target="_blank" class="btn btn-default"><i class="fa-regular fa-file-pdf"></i> Consignor Copy</a>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <?php if(isset($trip->consignee_id) && $trip->consignee_id): ?>
                                    <a href="<?php echo admin_url('tms/trips/pdf/' . $trip->id); ?>?output_type=I&type=consignee" target="_blank" class="btn btn-default"><i class="fa-regular fa-file-pdf"></i> Consignee Copy</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- Consignor Selection -->
                                    <?php 
                                    $selected_consignor = [];
                                    foreach($consignors as $key => $value) {
                                        $selected_consignor[] = [
                                            'id' => $key,
                                            'name' => $value
                                        ];
                                    }
                                    echo render_select('consignor_id', 
                                        $selected_consignor,
                                        ['id', 'name'],
                                        'consignor',
                                        $trip->consignor_id ?? '',
                                        ['data-live-search' => true]
                                    ); 
                                    ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- From City -->
                                        <?php echo render_input('from_city', 'From City', $trip->from_city ?? ''); ?>
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
                                            echo render_select('from_state', 
                                                $states,
                                                ['id', 'name'],
                                                'From state',
                                                $trip->from_state ?? '',
                                                ['required' => true, 'data-live-search' => true]
                                            ); 
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- Consingee Selection -->
                                    <?php 
                                    $selected_consignee = [];
                                    foreach($consignees as $key => $value) {
                                        $selected_consignee[] = [
                                            'id' => $key,
                                            'name' => $value
                                        ];
                                    }
                                    echo render_select('consignee_id', 
                                        $selected_consignee,
                                        ['id', 'name'],
                                        'consignee',
                                        $trip->consignee_id ?? '',
                                        ['data-live-search' => true]
                                    ); 
                                    ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- To City -->
                                        <?php echo render_input('to_city', 'To City', $trip->to_city ?? ''); ?>
                                    </div>
                                    <div class="col-md-6">
                                <!-- To State -->
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
                                        'To state',
                                        $trip->to_state ?? '',
                                        ['required' => true, 'data-live-search' => true]
                                        ); 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-6">
                                

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- GR Number -->
                                        <div class="form-group">
                                            <label for="gr_number"><?php echo _l('GR Number'); ?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <span id="prefix">GR-</span>
                                            </span>
                                            <?php if(isset($trip->gr_number) && !empty($trip->gr_number)): ?>
                                                <input type="text" name="gr_number" id="gr_number" class="form-control" value="<?php echo str_pad($trip->gr_number, 4, '0', STR_PAD_LEFT); ?>" data-unique="true">
                                            <?php else: ?>
                                                <input type="text" name="gr_number" id="gr_number" class="form-control" value="<?php echo isset($next_gr_number) ? $next_gr_number : ''; ?>" data-unique="true">
                                            <?php endif; ?>
                                            
                                        </div>
                                            <div id="gr_number_validation_message"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- driver Selection -->
                                        <?php 
                                            $selected_driver = [];
                                            foreach($drivers as $key => $value) {
                                                $selected_driver[] = [
                                                    'id' => $key,
                                                    'name' => $value
                                                ];
                                            }
                                            echo render_select('driver_id', 
                                                $selected_driver,
                                                ['id', 'name'],
                                                'driver',
                                                $trip->driver_id ?? '',
                                                ['data-live-search' => true]
                                            ); 
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Vehicle Number -->
                                        <?php echo render_input('vehicle_number', 'Vehicle Number', $trip->vehicle_number ?? '', 'text', ['maxlength' => 10, 'minlength' => 10, 'pattern' => '[A-Za-z0-9]{10}', 'style' => 'text-transform: uppercase;']); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Vehicle Owner -->
                                        <?php 
                                            $selected_vehicle_owner = [];
                                            // print_r($vehicle_owners);
                                            foreach($vehicle_owners as $key => $value) {
                                                $selected_vehicle_owner[] = [
                                                    'id' => $key,
                                                    'name' => $value
                                                ];
                                            }
                                            echo render_select('vehicle_owner_id', 
                                                $selected_vehicle_owner,
                                                ['id', 'name'],
                                                'Vehicle Owner',
                                                $trip->vehicle_owner_id ?? '',
                                                ['data-live-search' => true]
                                            ); 
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Party -->
                                        <?php 
                                            $selected_party = [];

                                            // print_r($parties);
                                            foreach($parties as $key => $value) {
                                                $selected_party[] = [
                                                    'id' => $key,
                                                    'name' => $value
                                                ];
                                            }
                                            echo render_select('party_id', 
                                                $selected_party,
                                                ['id', 'name'],
                                                'Third Party',
                                                $trip->party_id ?? '',
                                                ['data-live-search' => true]
                                            ); 
                                        ?>
                                    </div>
                                   
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo render_date_input('start_date', 'Trip Date', _d($trip->start_date ?? date('Y-m-d'))); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="status" class="control-label"><?php echo _l('trip_status'); ?></label>
                                            <select class="selectpicker display-block" name="status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value="scheduled" <?php echo (isset($trip->status) && $trip->status == 'scheduled') ? 'selected' : ''; ?>><?php echo _l('scheduled'); ?></option>
                                                <option value="in_transit" <?php echo (isset($trip->status) && $trip->status == 'in_transit') ? 'selected' : ''; ?>><?php echo _l('in transit'); ?></option>
                                                <option value="delivered" <?php echo (isset($trip->status) && $trip->status == 'delivered') ? 'selected' : ''; ?>><?php echo _l('delivered'); ?></option>
                                                <option value="cancelled" <?php echo (isset($trip->status) && $trip->status == 'cancelled') ? 'selected' : ''; ?>><?php echo _l('cancelled'); ?></option>
                                                <option value="delayed" <?php echo (isset($trip->status) && $trip->status == 'delayed') ? 'selected' : ''; ?>><?php echo _l('delayed'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="row">
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo render_input('consignor_invoice', 'Consignor Invoice No.', $trip->consignor_invoice ?? ''); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo render_date_input('invoice_date', 'Consignment Invoice Date', _d($trip->invoice_date ?? '')); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo render_input('total_freight', 'Total Freight Amount', $trip->total_freight ?? '', 'number'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo render_input('advance_amount', 'Advance Amount', isset($trip_meta['advance_amount']) ? $trip_meta['advance_amount'] : '', 'number'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo render_input('value_of_goods', 'Value of Goods', $trip->value_of_goods ?? '', 'number'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo render_input('labour_charges', 'Labour Charges', isset($trip_meta['labour_charges']) ? $trip_meta['labour_charges'] : '', 'number'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo render_input('gst_amount', 'GST Amount', isset($trip_meta['gst_amount']) ? $trip_meta['gst_amount'] : '', 'number'); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo render_input('consignment_weight', 'Consignment Weight', $trip->consignment_weight ?? '', 'number'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="consignment_bill" class="control-label">
                                                <?php echo _l('Consignment Bill'); ?>
                                                <i class="fa-regular fa-circle-question" data-toggle="tooltip" data-title="<?php echo _l('expense_add_edit_attach_receipt'); ?>"></i>
                                            </label>
                                            <input type="file" id="consignment_bill" name="consignment_bill" class="form-control" accept="image/*,application/pdf">
                                            <?php if(isset($trip_meta['consignment_bill']) && !empty($trip_meta['consignment_bill'])): ?>
                                                <div class="mtop10">
                                                    <a href="<?php echo site_url('uploads/tms/consignment_bills/' . $trip_meta['consignment_bill']); ?>" target="_blank">
                                                        <i class="fa fa-file"></i> View Consignment Bill
                                                    </a>
                                                    <input type="hidden" name="consignment_bill" value="<?php echo $trip_meta['consignment_bill']; ?>">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        

                        <!-- Trip Details -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table items table-main-items-create">
                                        <thead>
                                            <tr>
                                                <th width="60%">Item Name</th>
                                                <th width="30%">Quantity</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="item-rows">
                                            <?php 
                                            // Get items from trip_meta
                                            $items = [];
                                            if(isset($trip->id)) {  
                                                $items_meta = $this->db->get_where(db_prefix() . 'tms_trip_meta', ['trip_id' => $trip->id, 'meta_key' => 'items'])->row();
                                                if ($items_meta) {
                                                    $items = unserialize($items_meta->meta_value);
                                                }
                                                
                                                if (!empty($items)) {
                                                    foreach ($items as $item) { ?>
                                                        <tr>
                                                            <td>
                                                                <input type="text" name="item_name[]" class="form-control" placeholder="Item Name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="item_qty[]" class="form-control" placeholder="Quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" required>
                                                            </td>
                                                            <td>
                                                                <button type="button" onclick="remove_item_row(this);" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Remove</button>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                }
                                            } else {
                                                
                                            }
                                            ?>
                                            <tr class="main">
                                                <td>
                                                    <input type="text" class="form-control" placeholder="Item Name" name="item_name[]" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" placeholder="Quantity" name="item_qty[]" required>
                                                </td>
                                                <td>
                                                    <button type="button" onclick="add_item_row();" class="btn btn-info btn-xs"><i class="fa fa-plus"></i> Add More</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-md-12 mtop15">
                                <div class="btn-bottom-toolbar text-right">
                                    <button type="submit" class="btn btn-info mleft5 text-right transaction-submit">
                                        <?php echo _l('submit'); ?>
                                    </button>
                                    
                                </div>
                            </div>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
$(function(){
    // Initialize all selectpicker elements
    appSelectPicker();
    
    // Initialize datepicker
    init_datepicker();
    
    // Form validation
    appValidateForm($('#trip-form'), {
        consignor_id: 'required',
        consignee_id: 'required',
        gr_number: 'required',
        driver_id: 'required',
        vehicle_owner_id: 'required',
        vehicle_number: 'required',
        from_city: 'required',
        from_state: 'required',
        to_city: 'required',
        to_state: 'required',
        start_date: 'required',
        vehicle_number: 'required',
        consignor_invoice: 'required',
        invoice_date: 'required',
        value_of_goods: 'required',
        total_freight: 'required',
        consignment_weight: 'required',
        <?php if(!isset($trip->id)): ?>
            consignment_bill: 'required',
        <?php endif; ?>
        'item_name[]': 'required',
        'item_qty[]': {
            required: true,
            min: 1
        }
    });

    // Auto-populate addresses when consignor/consignee is selected
    $('#consignor_id').on('change', function() {
        var consignorId = $(this).val();
        if(consignorId) {
            $.get(admin_url + 'tms/consignors/get/' + consignorId, function(response) {
                var data = JSON.parse(response);
                if(data.success) {
                    $('#from_city').val(data.city);
                    $('#from_state').val(data.state).trigger('change');
                }
            });
        }
    });

    $('#consignee_id').on('change', function() {
        var consigneeId = $(this).val();
        if(consigneeId) {
            $.get(admin_url + 'tms/consignees/get/' + consigneeId, function(response) {
                var data = JSON.parse(response);
                console.log(data);
                if(data.success) {
                    $('#to_city').val(data.city);
                    $('#to_state').val(data.state).trigger('change');
                }
            });
        }
    });
});

function add_item_row() {
    var newRow = $(`<tr>
        <td><input type="text" name="item_name[]" class="form-control" placeholder="Item Name" required></td>
        <td><input type="number" name="item_qty[]" class="form-control" placeholder="Quantity" min="1" required></td>
        <td><button type="button" onclick="remove_item_row(this);" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Remove</button></td>
    </tr>`);
    
    // Insert the new row before the main row
    $('#item-rows tr.main').before(newRow);
    
    // Focus on the item name input of the new row
    newRow.find('input[name="item_name[]"]').focus();
}

function remove_item_row(button) {
    // Don't remove if it's the last row
    if ($('#item-rows tr').length > 1) {
        $(button).closest('tr').remove();
    }
}
</script> 