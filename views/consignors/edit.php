<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open(admin_url('tms/consignors/edit/' . $consignor->id), ['id' => 'consignor-form']); ?>
                        <h4 class="no-margin"><?php echo _l('Edit Consignor'); ?></h4>
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('company', 'company_name', $consignor->company, 'text', ['required' => true]); ?>
                                <?php echo render_input('contact_person', 'contact_person', $consignor->contact_person, 'text', ['required' => true]); ?>
                                <?php echo render_input('email', 'email', $consignor->email, 'email'); ?>
                                <?php echo render_input('phone', 'phone', $consignor->phone, 'tel', ['required' => true]); ?>
                                <?php echo render_input('gst', 'gst_number', $consignor->gst ?? '', 'text', ['maxlength' => 15, 'minlength' => 15, 'style' => 'text-transform: uppercase;']); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_textarea('address', 'address', $consignor->address); ?>
                                <?php echo render_input('city', 'city', $consignor->city, 'text', ['required' => true]); ?>
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
                                        $consignor->state,
                                        ['data-live-search' => true]
                                    ); 
                                ?>
                                <?php echo render_input('postal_code', 'postal_code', $consignor->postal_code); ?>
                                <?php echo render_input('country', 'country', 'India', 'text', ['readonly' => true]); ?>
                                <div class="form-group">
                                    <label for="status"><?php echo _l('status'); ?></label>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="status" id="status" <?php if ($consignor->status == 1) {echo 'checked';} ?>>
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
    // $('select[name="state"]').select2({
    //     placeholder: "Select State",
    //     allowClear: true,
    //     width: '100%'
    // });
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
