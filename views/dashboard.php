<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo _l('transport_management_dashboard'); ?></h4>
                        <hr class="hr-panel-heading" />
                        
                        <div class="row">
                            <!-- Active Vehicles -->
                            <div class="col-md-4">
                                <div class="widget-box">
                                    <div class="widget-header">
                                        <h4 class="widget-title lighter smaller">
                                            <i class="fa fa-truck"></i> <?php echo _l('active_vehicles'); ?>
                                        </h4>
                                    </div>
                                    <div class="widget-body">
                                        <div class="widget-main padding-8">
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <h3 class="bold">
                                                        <?php
                                                        $total_vehicles = $this->db->where('status', 'active')->count_all_results(db_prefix() . 'tms_vehicles');
                                                        echo $total_vehicles;
                                                        ?>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Drivers -->
                            <div class="col-md-4">
                                <div class="widget-box">
                                    <div class="widget-header">
                                        <h4 class="widget-title lighter smaller">
                                            <i class="fa fa-users"></i> <?php echo _l('active_drivers'); ?>
                                        </h4>
                                    </div>
                                    <div class="widget-body">
                                        <div class="widget-main padding-8">
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <h3 class="bold">
                                                        <?php
                                                        $total_drivers = $this->db->where('status', 'active')->count_all_results(db_prefix() . 'tms_drivers');
                                                        echo $total_drivers;
                                                        ?>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Trips -->
                            <div class="col-md-4">
                                <div class="widget-box">
                                    <div class="widget-header">
                                        <h4 class="widget-title lighter smaller">
                                            <i class="fa fa-road"></i> <?php echo _l('active_trips'); ?>
                                        </h4>
                                    </div>
                                    <div class="widget-body">
                                        <div class="widget-main padding-8">
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <h3 class="bold">
                                                        <?php
                                                        $total_trips = $this->db->where('status', 'scheduled')->count_all_results(db_prefix() . 'tms_trips');
                                                        echo $total_trips;
                                                        ?>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mtop20">
                            <div class="col-md-12">
                                <h4><?php echo _l('recent_trips'); ?></h4>
                                <hr class="hr-panel-heading" />
                                <table class="table dt-table table-trips" data-order-col="6" data-order-type="desc">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('id'); ?></th>
                                            <th><?php echo _l('vehicle'); ?></th>
                                            <th><?php echo _l('driver'); ?></th>
                                            <th><?php echo _l('start_location'); ?></th>
                                            <th><?php echo _l('end_location'); ?></th>
                                            <th><?php echo _l('status'); ?></th>
                                            <th><?php echo _l('start_date'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $this->db->limit(10);
                                        $this->db->order_by('start_date', 'desc');
                                        $trips = $this->db->get(db_prefix() . 'tms_trips')->result_array();
                                        foreach ($trips as $trip) {
                                            ?>
                                            <tr>
                                                <td><?php echo $trip['id']; ?></td>
                                                <td><?php echo get_vehicle_name($trip['vehicle_id']); ?></td>
                                                <td><?php echo get_driver_name($trip['driver_id']); ?></td>
                                                <td><?php echo $trip['start_location']; ?></td>
                                                <td><?php echo $trip['end_location']; ?></td>
                                                <td><?php echo $trip['status']; ?></td>
                                                <td data-order="<?php echo strtotime($trip['start_date']); ?>">
                                                    <?php echo _dt($trip['start_date']); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-trips', window.location.href);
});
</script> 