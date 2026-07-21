<?php
namespace App\Models;

use CodeIgniter\Model;

class AccountingModel extends Model
{
    protected $table = 'accounting_transactions';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'transaction_date', 'type', 'account',
        'description', 'amount', 'reference',
        'status', 'created_by'
    ];
    
    // Metode untuk accounting
}