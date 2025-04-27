<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Parties_model extends App_Model
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
     * Get party by ID
     * @param  mixed $id party id
     * @return mixed     object or false if not found
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tms_parties')->row();
        }
        return $this->db->get(db_prefix() . 'tms_parties')->result_array();
    }

    /**
     * Add new party
     * @param array $data party data
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->insert(db_prefix() . 'tms_parties', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Party Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update party
     * @param  array $data party data
     * @param  mixed $id   party id
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_parties', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Party Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete party
     * @param  mixed $id party id
     * @return boolean
     */
    public function delete($id)
    {
        // Check if party has any related trips
        $this->db->where('party_id', $id);
        $trips = $this->db->get(db_prefix() . 'tms_trips')->result_array();

        if (count($trips) > 0) {
            return [
                'success' => false,
                'message' => _l('party_has_trips')
            ];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tms_parties');

        if ($this->db->affected_rows() > 0) {
            log_activity('Party Deleted [ID: ' . $id . ']');
            return [
                'success' => true,
                'message' => _l('deleted_successfully', _l('party'))
            ];
        }

        return [
            'success' => false,
            'message' => _l('problem_deleting', _l('party'))
        ];
    }

    /**
     * Get parties for dropdown
     * @return array
     */
    public function get_parties_for_dropdown()
    {
        $this->db->select('id, company');
        $this->db->where('status', 1);
        $parties = $this->db->get(db_prefix() . 'tms_parties')->result_array();
        
        $dropdown = [];
        foreach ($parties as $party) {
            $dropdown[$party['id']] = $party['company'];
        }

        return $dropdown;
    }

    /**
     * Change party status
     * @param  mixed $id     party id
     * @param  mixed $status status
     * @return boolean
     */
    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_parties', ['status' => $status]);
        
        return $this->db->affected_rows() > 0;
    }


} 