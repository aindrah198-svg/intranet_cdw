<?php

namespace App\Controllers\Teknisi;

class InfoClient extends TeknisiController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $search = $this->request->getGet('search');

        // Fetch client contacts from centralized sales_klien / client / projects table
        $clients = [];
        if ($db->tableExists('sales_klien')) {
            $builder = $db->table('sales_klien')->where('deleted_at IS NULL');
            if ($search) {
                $builder->groupStart()
                    ->like('nama_klien', $search)
                    ->orLike('perusahaan', $search)
                ->groupEnd();
            }
            $clients = $builder->get()->getResultArray();
        } elseif ($db->tableExists('client')) {
            $builder = $db->table('client');
            if ($search) {
                $builder->like('nama_client', $search)->orLike('perusahaan', $search);
            }
            $clients = $builder->get()->getResultArray();
        }

        $data = [
            'title' => 'Info Client Project',
            'active' => 'tugas-proyek',
            'clients' => $clients,
            'search' => $search
        ];

        return $this->renderView('teknisi/info_client/index', $data);
    }

    public function detail($id)
    {
        $db = \Config\Database::connect();
        $client = null;
        if ($db->tableExists('sales_klien')) {
            $client = $db->table('sales_klien')->where('id', $id)->get()->getRowArray();
        } elseif ($db->tableExists('client')) {
            $client = $db->table('client')->where('id', $id)->get()->getRowArray();
        }

        $data = [
            'title' => 'Detail Info Client',
            'active' => 'tugas-proyek',
            'client' => $client
        ];

        return $this->renderView('teknisi/info_client/detail', $data);
    }
}
