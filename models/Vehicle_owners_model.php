<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vehicle_owners_model extends App_Model
{
    private $db_fields = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'gst',
        'status',
        'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get vehicle owner by ID
     * @param  mixed $id vehicle owner id
     * @return mixed     object or false if not found
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tms_vehicle_owners')->row();
        }
        return $this->db->get(db_prefix() . 'tms_vehicle_owners')->result_array();
    }

    /**
     * Add new vehicle owner
     * @param array $data vehicle owner data
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->insert(db_prefix() . 'tms_vehicle_owners', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Vehicle Owner Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update vehicle owner
     * @param  array $data vehicle owner data
     * @param  mixed $id   vehicle owner id
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_vehicle_owners', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Vehicle Owner Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete vehicle owner
     * @param  mixed $id vehicle owner id
     * @return boolean
     */
    public function delete($id)
    {
        // Check if vehicle owner has any related trips
        $this->db->where('vehicle_owner_id', $id);
        $trips = $this->db->get(db_prefix() . 'tms_trips')->result_array();

        if (count($trips) > 0) {
            return [
                'success' => false,
                'message' => _l('vehicle_owner_has_trips')
            ];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tms_vehicle_owners');

        if ($this->db->affected_rows() > 0) {
            log_activity('Vehicle Owner Deleted [ID: ' . $id . ']');
            return [
                'success' => true,
                'message' => _l('deleted_successfully', _l('vehicle_owner'))
            ];
        }

        return [
            'success' => false,
            'message' => _l('problem_deleting', _l('vehicle_owner'))
        ];
    }

    /**
     * Get vehicle owners for dropdown
     * @return array
     */
    public function get_vehicle_owners_for_dropdown()
    {
        $this->db->select('id, name');
        $this->db->where('status', 1);
        $vehicle_owners = $this->db->get(db_prefix() . 'tms_vehicle_owners')->result_array();
        
        $dropdown = [];
        foreach ($vehicle_owners as $vehicle_owner) {
            $dropdown[$vehicle_owner['id']] = $vehicle_owner['name'];
        }

        return $dropdown;
    }

    /**
     * Change vehicle owner status
     * @param  mixed $id     vehicle owner id
     * @param  mixed $status status
     * @return boolean
     */
    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_vehicle_owners', ['status' => $status]);
        
        return $this->db->affected_rows() > 0;
    }


} 