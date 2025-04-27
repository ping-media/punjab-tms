<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Trips_model extends App_Model
{
    private $db_fields = [
        'gr_number',
        'consignor_id',
        'driver_id',
        'vehicle_id',
        'vehicle_owner_id',
        'party_id',
        'from_location',
        'to_location',
        'status',
        'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db_fields = [
            'gr_number',
            'consignor_id',
            'driver_id',
            'vehicle_id',
            'vehicle_owner_id',
            'party_id',
            'from_location',
            'to_location',
            'status',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get trip by ID
     * @param  integer $id trip id
     * @return object
     */
    public function get($id = '')
    {
        if ($id) {
            // First get the trip data with joins
            $this->db->select('tbltms_trips.*, tbltms_consignors.company as consignor_name, tbltms_consignees.company as consignee_name, tbltms_drivers.name as driver_name, tbltms_drivers.phone as driver_phone, tbltms_vehicle_owners.name as vehicle_owner_name');
            $this->db->join(db_prefix() . 'tms_consignors', db_prefix() . 'tms_consignors.id = ' . db_prefix() . 'tms_trips.consignor_id', 'left');
            $this->db->join(db_prefix() . 'tms_consignees', db_prefix() . 'tms_consignees.id = ' . db_prefix() . 'tms_trips.consignee_id', 'left');
            $this->db->join(db_prefix() . 'tms_drivers', db_prefix() . 'tms_drivers.id = ' . db_prefix() . 'tms_trips.driver_id', 'left');
            $this->db->join(db_prefix() . 'tms_vehicle_owners', db_prefix() . 'tms_vehicle_owners.id = ' . db_prefix() . 'tms_trips.vehicle_owner_id', 'left');
            $this->db->where('tbltms_trips.id', $id);
            $trip = $this->db->get(db_prefix() . 'tms_trips')->row();

            if ($trip) {
                // Get all meta values for this trip
                $this->db->where('trip_id', $id);
                $meta = $this->db->get(db_prefix() . 'tms_trip_meta')->result_array();
                
                // Add meta values as properties to trip object
                foreach ($meta as $m) {
                    if ($m['meta_key'] === 'items') {
                        $trip->{$m['meta_key']} = unserialize($m['meta_value']);
                    } else {
                        $trip->{$m['meta_key']} = $m['meta_value'];
                    }
                }
            }

            return $trip;
        }
        
        $this->db->order_by('id', 'DESC');
        return $this->db->get(db_prefix() . 'tms_trips')->result_array();
    }

    /**
     * Get trip all details by ID
     * @param  integer $id trip id
     * @return object
     */
    public function get_all_details($id = '')
    {
        if ($id) {
            $this->db->select('tbltms_trips.*, tbltms_consignors.company as consignor_name, tbltms_consignees.company as consignee_name, tbltms_drivers.name as driver_name, tbltms_consignees.address as consignee_address, tbltms_consignees.city as consignee_city, tbltms_consignees.state as consignee_state, tbltms_consignees.country as consignee_country, tbltms_consignees.postal_code as consignee_postal_code, tbltms_consignees.gst as consignee_gst, tbltms_consignors.address as consignor_address, tbltms_consignors.city as consignor_city, tbltms_consignors.state as consignor_state, tbltms_consignors.country as consignor_country, tbltms_consignors.postal_code as consignor_postal_code, tbltms_consignors.gst as consignor_gst');
            $this->db->join(db_prefix() . 'tms_consignors', db_prefix() . 'tms_consignors.id = ' . db_prefix() . 'tms_trips.consignor_id', 'left');
            $this->db->join(db_prefix() . 'tms_consignees', db_prefix() . 'tms_consignees.id = ' . db_prefix() . 'tms_trips.consignee_id', 'left');
            $this->db->join(db_prefix() . 'tms_drivers', db_prefix() . 'tms_drivers.id = ' . db_prefix() . 'tms_trips.driver_id', 'left');

            $this->db->where('tbltms_trips.id', $id);
            $trip = $this->db->get(db_prefix() . 'tms_trips')->row();

            if ($trip) {
                // Get all meta values for this trip
                $this->db->where('trip_id', $id);
                $meta = $this->db->get(db_prefix() . 'tms_trip_meta')->result_array();
                
                // Add meta values as properties to trip object
                foreach ($meta as $m) {
                    if ($m['meta_key'] === 'items') {
                        $trip->{$m['meta_key']} = unserialize($m['meta_value']);
                    } else {
                        $trip->{$m['meta_key']} = $m['meta_value'];
                    }
                }
            }

            return $trip;
        }
        
        // For listing all trips, include basic meta data
        $trips = $this->db->get(db_prefix() . 'tms_trips')->result_array();
        
        foreach ($trips as &$trip) {
            $this->db->where('trip_id', $trip['id']);
            $meta = $this->db->get(db_prefix() . 'tms_trip_meta')->result_array();
            
            foreach ($meta as $m) {
                if ($m['meta_key'] === 'items') {
                    $trip[$m['meta_key']] = unserialize($m['meta_value']);
                } else {
                    $trip[$m['meta_key']] = $m['meta_value'];
                }
            }
        }
        
        return $trips;
    }

    /**
     * Add new trip
     * @param array $data trip data
     */
    public function add($data)
    {

        
        // Check if GR number exists
        if ($this->check_gr_number_exists($data['gr_number'])) {
            return false;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        // $data['status'] = isset($data['status']) ? $data['status'] : 1;

        // Filter out any data not in $db_fields
        $db_fields = $this->db->list_fields(db_prefix() . 'tms_trips');
        $data = array_intersect_key($data, array_flip($db_fields));

        $this->db->insert(db_prefix() . 'tms_trips', $data);
        $insert_id = $this->db->insert_id();

        

        if ($insert_id) {
            log_activity('New Trip Created [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update trip
     * @param  array $data trip data
     * @param  integer $id   trip id
     * @return boolean
     */
    public function update($data, $id)
    {
        // Check if GR number exists for other trips
        if ($this->check_gr_number_exists($data['gr_number'], $id)) {
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_trips', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Trip Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete trip
     * @param  integer $id trip id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tms_trips');

        if ($this->db->affected_rows() > 0) {
            log_activity('Trip Deleted [ID: ' . $id . ']');
            return [
                'success' => true,
                'message' => _l('deleted', _l('trip'))
            ];
        }

        return [
            'success' => false,
            'message' => _l('problem_deleting', _l('trip'))
        ];
    }

    /**
     * Update trip status
     * @param  integer $id     trip id
     * @param  string  $status new status
     * @return boolean
     */
    public function update_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_trips', ['status' => $status]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Trip Status Updated [ID: ' . $id . ', Status: ' . $status . ']');
            return true;
        }

        return false;
    }

    /**
     * Generate unique trip number
     * @return string
     */
    private function generate_trip_number()
    {
        $prefix = 'TRP';
        $next_number = get_option('next_trip_number');
        
        if (!$next_number) {
            $next_number = 1;
            add_option('next_trip_number', 1);
        }

        $trip_number = $prefix . str_pad($next_number, 6, '0', STR_PAD_LEFT);
        
        // Increment the next number
        update_option('next_trip_number', $next_number + 1);
        
        return $trip_number;
    }

    /**
     * Get trips by driver
     * @param  mixed $driver_id
     * @return array
     */
    public function get_trips_by_driver($driver_id)
    {
        $this->db->where('driver_id', $driver_id);
        return $this->db->get(db_prefix() . 'tms_trips')->result_array();
    }

    /**
     * Get trips by consignor
     * @param  mixed $consignor_id
     * @return array
     */
    public function get_trips_by_consignor($consignor_id)
    {
        $this->db->where('consignor_id', $consignor_id);
        return $this->db->get(db_prefix() . 'tms_trips')->result_array();
    }

    /**
     * Get all trips with related data
     * @return array
     */
    public function get_all_trips()
    {
        $this->db->select([
            db_prefix() . 'tms_trips.*',
            db_prefix() . 'tms_consignors.company as company',
            db_prefix() . 'tms_consignors.phone as consignor_phone',
            db_prefix() . 'tms_consignees.company as consignee_name', 
            db_prefix() . 'tms_consignees.phone as consignee_phone',
            db_prefix() . 'tms_drivers.name as driver_name',
            db_prefix() . 'tms_drivers.phone as driver_phone'
        ]);
        
        $this->db->join(db_prefix() . 'tms_consignors', db_prefix() . 'tms_consignors.id = ' . db_prefix() . 'tms_trips.consignor_id', 'left');
        $this->db->join(db_prefix() . 'tms_consignees', db_prefix() . 'tms_consignees.id = ' . db_prefix() . 'tms_trips.consignee_id', 'left');
        $this->db->join(db_prefix() . 'tms_drivers', db_prefix() . 'tms_drivers.id = ' . db_prefix() . 'tms_trips.driver_id', 'left');
        
        $this->db->order_by(db_prefix() . 'tms_trips.id', 'desc');
        
        return $this->db->get(db_prefix() . 'tms_trips')->result_array();
    }

    /**
     * Get next GR number
     * @return string
     */
    public function get_next_number()
    {
        $this->db->select('gr_number');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $last_trip = $this->db->get(db_prefix() . 'tms_trips')->row();

        if ($last_trip) {
            // Extract number from GR-XXXX format
            $number = intval(str_replace('GR-', '', $last_trip->gr_number));
            $next_number = $number + 1;
        } else {
            $next_number = 1;
        }

        return str_pad($next_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if GR number already exists
     * @param  string  $gr_number GR number to check
     * @param  integer $trip_id   Trip ID to exclude from check
     * @return boolean
     */
    public function check_gr_number_exists($gr_number, $trip_id = null)
    {
        $this->db->where('gr_number', $gr_number);
        if ($trip_id) {
            $this->db->where('id !=', $trip_id);
        }
        
        return $this->db->get(db_prefix() . 'tms_trips')->num_rows() > 0;
    }
} 