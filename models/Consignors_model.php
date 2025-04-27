<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Consignors_model extends App_Model
{
    private $db_fields = [
        'company',
        'contact_person',
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
     * Get consignor by ID
     * @param  mixed $id consignor id
     * @return mixed     object or false if not found
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tms_consignors')->row();
        }
        return $this->db->get(db_prefix() . 'tms_consignors')->result_array();
    }

    /**
     * Get consignor trips
     * @param  mixed $id consignor id
     * @return mixed     object or false if not found
     */
    public function get_consignor_trips($id = '')
    {
        if ($id) {
            $this->db->select('tms_consignors.*, tms_trips.id as trip_id, tms_trips.gr_number, tms_trips.start_date')
                     ->from(db_prefix() . 'tms_consignors')
                     ->join(db_prefix() . 'tms_trips', 'tms_trips.consignor_id = tms_consignors.id', 'left')
                     ->where('tms_consignors.id', $id);
            
            $result = $this->db->get()->row();
            
            
            if ($result) {
                // Get trips data
                $this->db->select('id, gr_number, start_date')
                         ->from(db_prefix() . 'tms_trips')
                         ->where('consignor_id', $id);
                $trips = $this->db->get()->result_array();
                
                $result->trips = $trips;
                return $result;
            }
            
            return false;
        }
        return $this->db->get(db_prefix() . 'tms_consignors')->result_array();
    }

    /**
     * Add new consignor
     * @param array $data consignor data
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->insert(db_prefix() . 'tms_consignors', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Consignor Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update consignor
     * @param  array $data consignor data
     * @param  mixed $id   consignor id
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_consignors', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Consignor Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete consignor
     * @param  mixed $id consignor id
     * @return boolean
     */
    public function delete($id)
    {
        // Check if consignor has any related trips
        $this->db->where('consignor_id', $id);
        $trips = $this->db->get(db_prefix() . 'tms_trips')->result_array();

        if (count($trips) > 0) {
            return [
                'success' => false,
                'message' => _l('consignor_has_trips')
            ];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tms_consignors');

        if ($this->db->affected_rows() > 0) {
            log_activity('Consignor Deleted [ID: ' . $id . ']');
            return [
                'success' => true,
                'message' => _l('deleted_successfully', _l('consignor'))
            ];
        }

        return [
            'success' => false,
            'message' => _l('problem_deleting', _l('consignor'))
        ];
    }

    /**
     * Get consignors for dropdown
     * @return array
     */
    public function get_consignors_for_dropdown()
    {
        $this->db->select('id, company');
        $this->db->where('status', 1);
        $consignors = $this->db->get(db_prefix() . 'tms_consignors')->result_array();
        
        $dropdown = [];
        foreach ($consignors as $consignor) {
            $dropdown[$consignor['id']] = $consignor['company'];
        }

        return $dropdown;
    }

    /**
     * Change consignor status
     * @param  mixed $id     consignor id
     * @param  mixed $status status
     * @return boolean
     */
    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_consignors', ['status' => $status]);
        
        return $this->db->affected_rows() > 0;
    }


} 