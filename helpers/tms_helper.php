<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get vehicle name by ID
 * @param  mixed $id vehicle id
 * @return string
 */
function get_vehicle_name($id)
{
    $CI = &get_instance();
    $CI->db->select('vehicle_number');
    $CI->db->where('id', $id);
    return $CI->db->get(db_prefix() . 'tms_vehicles')->row()->vehicle_number;
}

/**
 * Get driver name by ID
 * @param  mixed $id driver id
 * @return string
 */
function get_driver_name($id)
{
    $CI = &get_instance();
    $CI->db->select('CONCAT(staff.firstname, " ", staff.lastname) as full_name');
    $CI->db->from(db_prefix() . 'tms_drivers drivers');
    $CI->db->join(db_prefix() . 'staff staff', 'staff.staffid = drivers.staff_id', 'left');
    $CI->db->where('drivers.id', $id);
    return $CI->db->get()->row()->full_name;
}

/**
 * Get trip status badge
 * @param  string $status trip status
 * @return string
 */
function get_trip_status_badge($status)
{
    $status_classes = [
        'scheduled'    => 'info',
        'in_progress' => 'warning',
        'completed'   => 'success',
        'cancelled'   => 'danger'
    ];

    $class = isset($status_classes[$status]) ? $status_classes[$status] : 'default';
    return '<span class="label label-' . $class . '">' . _l('status_' . $status) . '</span>';
}

/**
 * Check if staff member can manage TMS
 * @param  mixed $staff_id staff id (if passed)
 * @return boolean
 */
function staff_can_manage_tms($staff_id = '')
{
    $CI = &get_instance();
    $staff_id = $staff_id == '' ? get_staff_user_id() : $staff_id;

    return ($CI->staff_model->is_admin($staff_id) || has_permission('tms', '', 'view'));
}

/**
 * Get vehicle types
 * @return array
 */
function get_vehicle_types()
{
    return [
        'truck'      => _l('truck'),
        'van'        => _l('van'),
        'bus'        => _l('bus'),
        'car'        => _l('car'),
        'motorcycle' => _l('motorcycle')
    ];
}

/**
 * Get trip statuses
 * @return array
 */
function get_trip_statuses()
{
    return [
        'scheduled'    => _l('status_scheduled'),
        'in_progress' => _l('status_in_progress'),
        'completed'   => _l('status_completed'),
        'cancelled'   => _l('status_cancelled')
    ];
}

/**
 * Get list of Indian states
 * @return array
 */
function get_indian_states()
{
    return [
        'AN' => 'Andaman and Nicobar Islands',
        'AP' => 'Andhra Pradesh',
        'AR' => 'Arunachal Pradesh',
        'AS' => 'Assam',
        'BR' => 'Bihar',
        'CH' => 'Chandigarh',
        'CT' => 'Chhattisgarh',
        'DN' => 'Dadra and Nagar Haveli',
        'DD' => 'Daman and Diu',
        'DL' => 'Delhi',
        'GA' => 'Goa',
        'GJ' => 'Gujarat',
        'HR' => 'Haryana',
        'HP' => 'Himachal Pradesh',
        'JK' => 'Jammu and Kashmir',
        'JH' => 'Jharkhand',
        'KA' => 'Karnataka',
        'KL' => 'Kerala',
        'LA' => 'Ladakh',
        'LD' => 'Lakshadweep',
        'MP' => 'Madhya Pradesh',
        'MH' => 'Maharashtra',
        'MN' => 'Manipur',
        'ML' => 'Meghalaya',
        'MZ' => 'Mizoram',
        'NL' => 'Nagaland',
        'OR' => 'Odisha',
        'PY' => 'Puducherry',
        'PB' => 'Punjab',
        'RJ' => 'Rajasthan',
        'SK' => 'Sikkim',
        'TN' => 'Tamil Nadu',
        'TG' => 'Telangana',
        'TR' => 'Tripura',
        'UP' => 'Uttar Pradesh',
        'UK' => 'Uttarakhand',
        'WB' => 'West Bengal'
    ];
} 

/**
 * Prepare general trip pdf
 * @param  object $trip Trip as object with all necessary fields
 * @param  string $tag  tag for bulk pdf exporter
 * @return mixed object
 */
function trip_pdf($trip, $tag = '', $copy_type = '')
{   
    try {
        $pdf = app_pdf('trip', FCPATH . 'modules/tms/libraries/Trip_pdf', $trip, $tag, $copy_type);

        // print_r($pdf);
        
        if ($pdf->getNumPages() == 0) {
            throw new Exception('PDF has 0 pages');
        }
        
        return $pdf;
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        echo $message;
        if (strpos($message, 'Unable to get the size of the image') !== false) {
            show_pdf_unable_to_get_image_size_error();
        }
        die;
    }
}

/**
 * Prepare freight invoice pdf
 * @param  object $invoice Invoice as object with all necessary fields
 * @param  string $tag  tag for bulk pdf exporter
 * @return mixed object
 */
function freight_invoice_pdf($freight_invoice, $tag = '')
{   
    try {
        $pdf = app_pdf('freight_invoice', FCPATH . 'modules/tms/libraries/Freight_invoice_pdf', $freight_invoice, $tag, 'L');

        // echo '<pre>';
        // print_r($freight_invoice);
        // echo '</pre>';
        // die;
        
        if ($pdf->getNumPages() == 0) {
            throw new Exception('PDF has 0 pages');
        }
        
        return $pdf;
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        echo $message;
        if (strpos($message, 'Unable to get the size of the image') !== false) {
            show_pdf_unable_to_get_image_size_error();
        }
        die;
    }
}

/**
 * Format trip number
 * @param  int $trip_number trip number
 * @return string
 */
function format_trip_number($trip_number)
{
    return 'GR-' . str_pad($trip_number, 4, '0', STR_PAD_LEFT);
}

/**
 * Format invoice number
 * @param  int $invoice_number invoice number
 * @return string
 */
function format_freight_invoice_number($invoice_number)
{
    return 'INV-' . str_pad($invoice_number, 4, '0', STR_PAD_LEFT);
}

/**
 * Convert number to words
 * @param  int $number number
 * @return string
 */
function numberToWords($number) {
    $ones = array(
        0 => "", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five",
        6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine", 10 => "Ten",
        11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen",
        16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen"
    );
    $tens = array(
        2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty",
        6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
    );
    
    if ($number == 0) {
        return "Zero";
    }

    $words = "";
    
    // Handling crores
    if ($number >= 10000000) {
        $words .= numberToWords(floor($number/10000000)) . " Crore ";
        $number = $number%10000000;
    }
    
    // Handling lakhs
    if ($number >= 100000) {
        $words .= numberToWords(floor($number/100000)) . " Lakh ";
        $number = $number%100000;
    }
    
    // Handling thousands
    if ($number >= 1000) {
        $words .= numberToWords(floor($number/1000)) . " Thousand ";
        $number = $number%1000;
    }
    
    // Handling hundreds
    if ($number >= 100) {
        $words .= numberToWords(floor($number/100)) . " Hundred ";
        $number = $number%100;
    }
    
    if ($number > 0) {
        if ($words != "") {
            $words .= "and ";
        }
        
        if ($number < 20) {
            $words .= $ones[$number];
        } else {
            $words .= $tens[floor($number/10)];
            if ($number%10 > 0) {
                $words .= " " . $ones[$number%10];
            }
        }
    }
    
    return trim($words);
}

/**
 * Format amount with rupee symbol
 * 
 * @param float $amount The amount to format
 * @param bool $format Whether to apply number_format (default: true)
 * @param int $decimals Number of decimal places (default: 2)
 * @return string Formatted amount with rupee symbol
 */
function format_rupee($amount, $format = true, $decimals = 2)
{
    if ($format) {
        return '₹' . number_format((float)$amount, $decimals);
    }
    
    return '₹' . $amount;
}




