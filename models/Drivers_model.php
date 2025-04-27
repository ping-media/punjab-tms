<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Drivers_model extends App_Model
{
    private $db_fields = [
        'name',
        'license_number',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'status',
        'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get driver by ID
     * @param  mixed $id driver id
     * @return mixed     object or false if not found
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tms_drivers')->row();
        }
        return $this->db->get(db_prefix() . 'tms_drivers')->result_array();
    }

    /**
     * Add new driver
     * @param array $data driver data
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->insert(db_prefix() . 'tms_drivers', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Driver Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update driver
     * @param  array $data driver data
     * @param  mixed $id   driver id
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_drivers', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Driver Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Change driver status
     * @param  mixed $id     driver id
     * @param  mixed $status status
     * @return boolean
     */
    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_drivers', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete driver
     * @param  mixed $id driver id
     * @return boolean
     */
    public function delete($id)
    {
        // Check if driver has any related trips
        $this->db->where('driver_id', $id);
        $trips = $this->db->get(db_prefix() . 'tms_trips')->result_array();

        if (count($trips) > 0) {
            return [
                'success' => false,
                'message' => _l('driver_has_trips')
            ];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tms_drivers');

        if ($this->db->affected_rows() > 0) {
            log_activity('Driver Deleted [ID: ' . $id . ']');
            return [
                'success' => true,
                'message' => _l('deleted_successfully', _l('driver'))
            ];
        }

        return [
            'success' => false,
            'message' => _l('problem_deleting', _l('driver'))
        ];
    }

    /**
     * Get drivers for dropdown
     * @return array
     */
    public function get_drivers_for_dropdown()
    {
        $this->db->select('id, name');
        $this->db->where('status', 1);
        $drivers = $this->db->get(db_prefix() . 'tms_drivers')->result_array();
        
        $dropdown = [];
        foreach ($drivers as $driver) {
            $dropdown[$driver['id']] = $driver['name'];
        }
        return $dropdown;
    }

    /**
     * Get driver statistics
     * @param  mixed $driver_id
     * @return array
     */
    public function get_driver_statistics($driver_id)
    {
        // Get total trips
        $this->db->where('driver_id', $driver_id);
        $total_trips = $this->db->count_all_results(db_prefix() . 'tms_trips');

        // Get total distance
        $this->db->select_sum('distance');
        $this->db->where('driver_id', $driver_id);
        $total_distance = $this->db->get(db_prefix() . 'tms_trips')->row()->distance;

        // Get completed trips
        $this->db->where('driver_id', $driver_id);
        $this->db->where('status', 'completed');
        $completed_trips = $this->db->count_all_results(db_prefix() . 'tms_trips');

        return [
            'total_trips' => $total_trips,
            'total_distance' => $total_distance,
            'completed_trips' => $completed_trips,
            'completion_rate' => $total_trips > 0 ? ($completed_trips / $total_trips) * 100 : 0
        ];
    }
} 