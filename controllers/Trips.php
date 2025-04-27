<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Trips extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tms_model');
        $this->load->model('trips_model');
        $this->load->model('tms/consignors_model');
        $this->load->model('tms/consignees_model');
        $this->load->model('tms/drivers_model');
        $this->load->model('tms/vehicle_owners_model');
        $this->load->model('tms/parties_model');
        $this->load->helper('tms');
    }

    /* List all trips */
    public function index()
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        if ($this->input->is_ajax_request()) {
            // Check if the required DataTables parameters exist
            if ($this->input->post('draw') !== null) {
                return $this->app->get_table_data(module_views_path('tms', 'tables/trips'));
            }
        }

        $data['title'] = _l('trips');
        $this->load->view('trips/manage', $data);
    }

    public function get_trip_data($trip_id)
    {
        if(empty($trip_id)) {
            echo json_encode(['success' => false, 'message' => 'Trip ID is required']);
            return;
        }

        if(!is_numeric($trip_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid Trip ID']);
            return;
        }

        $trip = $this->trips_model->get($trip_id);
        echo json_encode(['success' => true, 'data' => $trip]);
    }

    /* Add new trip */
    public function create()
    {

        if ($this->input->post()) {
            $data = $this->input->post();

            // Upload freight bill
            $consignment_bill = '';
            if (isset($_FILES['consignment_bill']) && $_FILES['consignment_bill']['name'] != '') {
                $config['upload_path'] = './uploads/tms/consignment_bills/';
                $config['allowed_types'] = 'pdf|jpg|jpeg|png';
                $config['max_size'] = '5120'; // 5MB
                $config['encrypt_name'] = true;

                // Create directory if it doesn't exist
                if (!file_exists($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('consignment_bill')) {
                    $upload_data = $this->upload->data();
                    $consignment_bill = $upload_data['file_name'];
                } else {
                    set_alert('warning', $this->upload->display_errors());
                }
            }

            // Extract items data
            $items = [];
            if (isset($data['item_name']) && isset($data['item_qty'])) {
                for ($i = 0; $i < count($data['item_name']); $i++) {
                    if (!empty($data['item_name'][$i]) && !empty($data['item_qty'][$i])) {
                        $items[] = [
                            'name' => $data['item_name'][$i],
                            'quantity' => $data['item_qty'][$i]
                        ];
                    }
                }
            }

            unset($data['item_name'], $data['item_qty']);
            // Serialize items array
            $items_serialized = serialize($items);
            
            // Combine date and time
            if (isset($data['date']) && isset($data['time'])) {
                $data['start_datetime'] = $data['date'] . ' ' . $data['time'];
                unset($data['date'], $data['time']);
            }

            

            $trip_id = $this->trips_model->add($data);

            if ($trip_id && $trip_id > 0) {

                // Update trip meta using helper function
                $this->update_trip_meta($trip_id, 'items', $items_serialized);

                // Store consignment bill in trip_meta table
                $this->update_trip_meta($trip_id, 'consignment_bill', $consignment_bill);
                $this->update_trip_meta($trip_id, 'gst_amount', $data['gst_amount']);
                $this->update_trip_meta($trip_id, 'labour_charges', $data['labour_charges']);
                $this->update_trip_meta($trip_id, 'advance_amount', $data['advance_amount']);
                $this->update_trip_meta($trip_id, 'stationary_charges', $data['stationary_charges']);
                
                set_alert('success', _l('added_successfully', _l('trip')));
                redirect(admin_url('tms/trips'));
            }
        }

        $data['title'] = _l('new_trip');
        $data['consignors'] = $this->consignors_model->get();
        $data['consignees'] = $this->consignees_model->get();
        $data['drivers'] = $this->drivers_model->get();
        $data['vehicle_owners'] = $this->vehicle_owners_model->get();
        $data['parties'] = $this->parties_model->get();
        $data['next_gr_number'] = $this->trips_model->get_next_number();
        
        $this->load->view('trips/create', $data);
    }

    /* Edit trip */
    public function trip($id = '')
    {

        if ($this->input->post()) {
            $data = $this->input->post();

            // print_r($data);
            // die;

            // Upload freight bill
            $consignment_bill = '';
            if (isset($_FILES['consignment_bill']) && $_FILES['consignment_bill']['name'] != '') {
                $config['upload_path'] = './uploads/tms/consignment_bills/';
                $config['allowed_types'] = 'pdf|jpg|jpeg|png';
                $config['max_size'] = '5120'; // 5MB
                $config['encrypt_name'] = true;

                // Create directory if it doesn't exist
                if (!file_exists($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, true);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('consignment_bill')) {
                    $upload_data = $this->upload->data();
                    $consignment_bill = $upload_data['file_name'];
                } else {
                    set_alert('warning', $this->upload->display_errors());
                }
            }
            
            // Extract items data
            $items = [];
            if (isset($data['item_name']) && isset($data['item_qty'])) {
                for ($i = 0; $i < count($data['item_name']); $i++) {
                    if (!empty(trim($data['item_name'][$i])) && !empty(trim($data['item_qty'][$i]))) {
                        $items[] = [
                            'name' => trim($data['item_name'][$i]),
                            'quantity' => trim($data['item_qty'][$i])
                        ];
                    }
                }
            }

            unset($data['item_name'], $data['item_qty']);
            // Serialize items array
            $items_serialized = serialize($items);

            if ($id == '') {
                $trip_id = $this->trips_model->add($data);
                if ($trip_id && $trip_id > 0) {

                    // Update trip meta using helper function
                    $this->update_trip_meta($trip_id, 'items', $items_serialized);
    
                    // Store consignment bill in trip_meta table
                    $this->update_trip_meta($trip_id, 'consignment_bill', $consignment_bill);
                    $this->update_trip_meta($trip_id, 'gst_amount', $data['gst_amount']);
                    $this->update_trip_meta($trip_id, 'labour_charges', $data['labour_charges']);
                    $this->update_trip_meta($trip_id, 'advance_amount', $data['advance_amount']);
                    $this->update_trip_meta($trip_id, 'stationary_charges', $data['stationary_charges']);
                    
                    set_alert('success', _l('added_successfully', _l('trip')));
                    redirect(admin_url('tms/trips'));
                }
            }else{
                if ($this->trips_model->update($data, $id)) {
                    $trip_meta = $this->update_trip_meta($id, 'items', $items_serialized);
                    if($consignment_bill && !empty($consignment_bill)){
                        $trip_meta = $this->update_trip_meta($id, 'consignment_bill', $consignment_bill);
                    }
                    $trip_meta = $this->update_trip_meta($id, 'gst_amount', $data['gst_amount']);
                    $trip_meta = $this->update_trip_meta($id, 'labour_charges', $data['labour_charges']);
                    $trip_meta = $this->update_trip_meta($id, 'advance_amount', $data['advance_amount']);
                    $trip_meta = $this->update_trip_meta($id, 'stationary_charges', $data['stationary_charges']);
                    
                    set_alert('success', _l('updated_successfully', _l('trip')));
                }
            }
            
            
            redirect(admin_url('tms/trips'));
        }

        if ($id == '') {
            $title = "New Trip";
            $data['trip'] = $this->trips_model->get();
        } else {
            $title = "Edit Trip";
            $data['trip'] = $this->trips_model->get($id);
        }
        $data['consignors'] = $this->consignors_model->get_consignors_for_dropdown();
        $data['consignees'] = $this->consignees_model->get_consignees_for_dropdown();
        $data['drivers'] = $this->drivers_model->get_drivers_for_dropdown();
        $data['vehicle_owners'] = $this->vehicle_owners_model->get_vehicle_owners_for_dropdown();
        $data['parties'] = $this->parties_model->get_parties_for_dropdown();
        $data['title'] = $title;
        $data['next_gr_number'] = $this->trips_model->get_next_number();
        $data['trip_meta'] = $this->get_trip_meta($id);
        
        $this->load->view('trips/trip', $data);
    }

    /* Delete trip */
    public function delete($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            access_denied('tms');
        }

        $response = $this->trips_model->delete($id);
        if ($response['success']) {
            set_alert('success', $response['message']);
        } else {
            set_alert('warning', $response['message']);
        }

        redirect(admin_url('tms/trips'));
    }

    /* Generate PDF for trip */
    public function pdf($id = null)
    {
        if (!$id) {
            redirect(admin_url('tms/trips'));
        }

        $trip = $this->trips_model->get_all_details($id);

        // Check if type parameter is set in GET request
        $copy_type = $this->input->get('type');

        try {
            $pdf = trip_pdf($trip, '', $copy_type);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(mb_strtoupper(slug_it(format_trip_number($id)), 'UTF-8') . '.pdf', 'I');
        die();
    }

    /* Update trip status */
    public function update_status($id)
    {
        if (!has_permission('tms', '', 'edit')) {
            access_denied('tms');
        }

        if ($this->input->post()) {
            $status = $this->input->post('status');
            if ($this->trips_model->update_status($id, $status)) {
                set_alert('success', _l('updated_successfully', _l('trip_status')));
            }
        }

        redirect(admin_url('tms/trips'));
    }

    /**
     * Check if GR number is unique
     * @return json
     */
    public function check_gr_number()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if ($this->input->post()) {
            $gr_number = $this->input->post('gr_number');
            
            // Check if GR number exists
            $exists = $this->trips_model->check_gr_number_exists($gr_number);
            
            echo json_encode([
                'unique' => !$exists,
                'message' => $exists ? _l('gr_number_already_exists') : ''
            ]);
        }
    }

    /**
     * Update trip meta data
     * @param  integer $trip_id    Trip ID
     * @param  string  $meta_key   Meta key
     * @param  string  $meta_value Meta value
     * @return boolean
     */
    private function update_trip_meta($trip_id, $meta_key, $meta_value)
    {
        // Check if required parameters are not empty or null
        if (empty($trip_id) || empty($meta_key) || empty($meta_value)) {
            return false;
        }
        $this->db->where('trip_id', $trip_id);
        $this->db->where('meta_key', $meta_key);
        $existing_meta = $this->db->get(db_prefix() . 'tms_trip_meta')->row();
        
        if ($existing_meta) {
            $this->db->where('trip_id', $trip_id);
            $this->db->where('meta_key', $meta_key);
            return $this->db->update(db_prefix() . 'tms_trip_meta', ['meta_value' => $meta_value]);
        } else {
            return $this->db->insert(db_prefix() . 'tms_trip_meta', [
                'trip_id' => $trip_id,
                'meta_key' => $meta_key,
                'meta_value' => $meta_value
            ]);
        }
    }

    /**
     * Get trip meta data
     * @param  integer $trip_id  Trip ID
     * @param  string  $meta_key Meta key (optional)
     * @return mixed Returns single meta value if meta_key provided, array of all meta data if not
     */
    private function get_trip_meta($trip_id, $meta_key = '')
    {
        if (empty($trip_id)) {
            return false;
        }

        $this->db->where('trip_id', $trip_id);
        if (!empty($meta_key)) {
            $this->db->where('meta_key', $meta_key);
            $meta = $this->db->get(db_prefix() . 'tms_trip_meta')->row();
            return $meta ? $meta->meta_value : null;
        }

        $meta_data = [];
        $results = $this->db->get(db_prefix() . 'tms_trip_meta')->result();
        foreach ($results as $result) {
            $meta_data[$result->meta_key] = $result->meta_value;
        }
        return $meta_data;
    }

    /**
     * Upload consignment bill
     * @param  integer $id trip id
     * @return json
     */
    public function upload_consignment_bill($id)
    {
        if (!has_permission('tms', '', 'edit')) {
            ajax_access_denied();
        }

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => _l('invalid_trip_id')
            ]);
            return;
        }

        $config['upload_path'] = './uploads/tms/consignment_bills/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['max_size'] = '5120'; // 5MB
        $config['encrypt_name'] = true;

        // Create directory if it doesn't exist
        if (!file_exists($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $upload_data = $this->upload->data();
            $consignment_bill = $upload_data['file_name'];

            // Delete old file if exists
            $trip_meta = $this->get_trip_meta($id);
            if (isset($trip_meta['consignment_bill']) && !empty($trip_meta['consignment_bill'])) {
                $old_file = './uploads/tms/consignment_bills/' . $trip_meta['consignment_bill'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            // Update trip meta
            $this->update_trip_meta($id, 'consignment_bill', $consignment_bill);

            echo json_encode([
                'success' => true,
                'message' => _l('file_uploaded_successfully')
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $this->upload->display_errors('', '')
            ]);
        }
    }

    /**
     * Remove consignment bill
     * @param  integer $id trip id
     * @return json
     */
    public function remove_consignment_bill($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied')
            ]);
            return;
        }

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => _l('invalid_trip_id')
            ]);
            return;
        }

        $trip_meta = $this->get_trip_meta($id);
        if (isset($trip_meta['consignment_bill']) && !empty($trip_meta['consignment_bill'])) {
            $file = './uploads/tms/consignment_bills/' . $trip_meta['consignment_bill'];
            if (file_exists($file)) {
                unlink($file);
            }

            // Update trip meta
            $this->update_trip_meta($id, 'consignment_bill', '');

            echo json_encode([
                'success' => true,
                'message' => _l('deleted', _l('consignment_bill'))
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => _l('file_not_found')
            ]);
        }
    }
} 