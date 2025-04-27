<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open(admin_url('tms/consignees/edit/' . $consignee->id)); ?>
                        <h4 class="no-margin"><?php echo _l('edit_consignee'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('company', 'company_name', $consignee->company); ?>
                                <?php echo render_input('contact_person', 'contact_person', $consignee->contact_person); ?>
                                <?php echo render_input('email', 'email', $consignee->email, 'email'); ?>
                                <?php echo render_input('phone', 'phone', $consignee->phone, 'tel', ['maxlength' => 10, 'pattern' => '[0-9]{10}', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']); ?>
                                <?php echo render_input('gst', 'gst_number', $consignee->gst, 'text', ['maxlength' => 15, 'minlength' => 15, 'pattern' => '[A-Za-z0-9]{10}', 'style' => 'text-transform: uppercase;']); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_textarea('address', 'address', $consignee->address); ?>
                                <?php echo render_input('city', 'city', $consignee->city); ?>
                                <?php 
                                    $states = [];
                                    foreach(get_indian_states() as $key => $value) {
                                        $states[] = [
                                            'id' => $key,
                                            'name' => $value
                                        ];
                                    }
                                    echo render_select('state', 
                                        $states,
                                        ['id', 'name'],
                                        'state',
                                        $consignee->state,
                                        ['data-live-search' => true]
                                    ); 
                                ?>
                                <?php echo render_input('postal_code', 'postal_code', $consignee->postal_code); ?>
                                <?php echo render_input('country', 'country', 'India', 'text', ['readonly' => true]); ?>
                                <div class="form-group">
                                    <label for="status"><?php echo _l('status'); ?></label>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="status" id="status" <?php if ($consignee->status == 1) {echo 'checked';} ?>>
                                        <label for="status"><?php echo _l('active'); ?></label>
                                    </div>
                                </div>
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
        company: 'required',
        contact_person: 'required',
        phone: 'required',
        city: 'required',
        state: 'required'
    });

    // Initialize select2 for state dropdown
    $('select[name="state"]').select2({
        placeholder: "Select State",
        allowClear: true,
        width: '100%'
    });
});
</script> 