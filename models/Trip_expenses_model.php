<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Trip_expenses_model extends App_Model
{
    private $db_fields = [
        'id',
        'gr_number',
        'type',
        'value',
        'expense_date',
        'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get trip expense by ID
     * @param  mixed $id trip expense id
     * @return mixed     object or false if not found
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tms_trip_expenses')->row();
        }
        return $this->db->get(db_prefix() . 'tms_trip_expenses')->result_array();
    }

    /**
     * Add new trip expense
     * @param array $data trip expense data
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->insert(db_prefix() . 'tms_trip_expenses', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Trip Expense Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update trip expense
     * @param  array $data trip expense data
     * @param  mixed $id   trip expense id
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_trip_expenses', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Trip Expense Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete trip expense
     * @param  mixed $id trip expense id
     * @return boolean
     */
    public function delete($id)
    {
        // Check if trip expense has any related trips
        $this->db->where('trip_id', $id);
        $trips = $this->db->get(db_prefix() . 'tms_trips')->result_array();

        if (count($trips) > 0) {
            return [
                'success' => false,
                'message' => _l('trip_expense_has_trips')
            ];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tms_trip_expenses');

        if ($this->db->affected_rows() > 0) {
            log_activity('Consignor Deleted [ID: ' . $id . ']');
            return [
                'success' => true,
                'message' => _l('deleted_successfully', _l('trip_expense'))
            ];
        }

        return [
            'success' => false,
            'message' => _l('problem_deleting', _l('trip_expense'))
        ];
    }


} 