<?php
namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'lead_name', 'contact_person', 'phone', 'email',
        'company', 'status', 'value', 'probability',
        'expected_close', 'sales_person', 'notes'
    ];
    
    // Metode untuk sales
}