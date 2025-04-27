<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Consignees_model extends App_Model
{
    private $db_fields = [
        'company',
        'contact_person',
        'email',
        'phone',
        'gst',
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

    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tms_consignees')->row();
        }
        return $this->db->get(db_prefix() . 'tms_consignees')->result_array();
    }

    /**
     * Get consignee trips
     * @param  mixed $id consignee id
     * @return mixed     object or false if not found
     */
    public function get_consignee_trips($id = '')
    {
        if ($id) {
            $this->db->select('tms_consignees.*, tms_trips.id as trip_id, tms_trips.gr_number, tms_trips.start_date')
                     ->from(db_prefix() . 'tms_consignees')
                     ->join(db_prefix() . 'tms_trips', 'tms_trips.consignee_id = tms_consignees.id', 'left')
                     ->where('tms_consignees.id', $id);
            
            $result = $this->db->get()->row();
            
            
            if ($result) {
                // Get trips data
                $this->db->select('id, gr_number, start_date')
                         ->from(db_prefix() . 'tms_trips')
                         ->where('consignee_id', $id);
                $trips = $this->db->get()->result_array();
                
                $result->trips = $trips;
                return $result;
            }
            
            return false;
        }
        return $this->db->get(db_prefix() . 'tms_consignees')->result_array();
    }

    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        $this->db->insert(db_prefix() . 'tms_consignees', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Consignee Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    public function update($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_consignees', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Consignee Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tms_consignees');

        if ($this->db->affected_rows() > 0) {
            log_activity('Consignee Deleted [ID: ' . $id . ']');
            return [
                'success' => true,
                'message' => _l('deleted_successfully', _l('consignee'))
            ];
        }
        return [
            'success' => false,
            'message' => _l('problem_deleting', _l('consignee'))
        ];
    }

    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_consignees', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get consignors for dropdown
     * @return array
     */
    public function get_consignees_for_dropdown()
    {
        $this->db->select('id, company');
        $this->db->where('status', 1);
        $consignees = $this->db->get(db_prefix() . 'tms_consignees')->result_array();
        
        $dropdown = [];
        foreach ($consignees as $consignee) {
            $dropdown[$consignee['id']] = $consignee['company'];
        }

        return $dropdown;
    }
} 
