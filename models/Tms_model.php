<?php

class Tms_model extends CI_Model
{
    public function get_consignors_for_dropdown()
    {
        $this->db->select('id, company');
        $this->db->where('status', 1);
        return $this->db->get(db_prefix() . 'tms_consignors')->result_array();
    }

    public function get_drivers_for_dropdown()
    {
        $this->db->select('id, name');
        $this->db->where('status', 1);
        return $this->db->get(db_prefix() . 'tms_drivers')->result_array();
    }
} 