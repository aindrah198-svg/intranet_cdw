<?php

namespace App\Controllers\SoftwareEngineer;

use App\Models\SoftwareEngineer\SeDocModel;
use App\Models\SoftwareEngineer\SeSystemModel;

class DokumentasiTeknis extends BaseSeController
{
    protected $docModel;
    protected $systemModel;

    public function __construct()
    {
        $this->docModel    = new SeDocModel();
        $this->systemModel = new SeSystemModel();
    }

    public function dokumentasi()
    {
        $data = [
            'title'   => 'Dokumentasi Teknis per Sistem - Software Engineer',
            'active'  => 'dokumentasi-teknis',
            'sub'     => 'dokumentasi-sistem',
            'docs'    => $this->docModel->getDocsWithSystem('technical_doc'),
            'systems' => $this->systemModel->findAll()
        ];

        return view('software_engineer/dokumentasi_teknis/dokumentasi', $data);
    }

    public function storeDoc()
    {
        $this->docModel->save([
            'system_id'  => $this->request->getPost('system_id'),
            'kategori'   => 'technical_doc',
            'judul'      => $this->request->getPost('judul'),
            'versi_doc'  => $this->request->getPost('versi_doc') ?: '1.0',
            'content'    => $this->request->getPost('content'),
            'link_file'  => $this->request->getPost('link_file'),
            'updated_by' => session()->get('name') ?? session()->get('username')
        ]);

        return redirect()->to(base_url('software-engineer/dokumentasi-teknis/dokumentasi-sistem'))->with('success', 'Dokumentasi teknis berhasil disimpan.');
    }

    public function arsitektur()
    {
        $data = [
            'title'   => 'Arsitektur & Diagram Sistem - Software Engineer',
            'active'  => 'dokumentasi-teknis',
            'sub'     => 'arsitektur-sistem',
            'docs'    => $this->docModel->getDocsWithSystem('arsitektur'),
            'systems' => $this->systemModel->findAll()
        ];

        return view('software_engineer/dokumentasi_teknis/arsitektur', $data);
    }

    public function storeArsitektur()
    {
        $this->docModel->save([
            'system_id'  => $this->request->getPost('system_id'),
            'kategori'   => 'arsitektur',
            'judul'      => $this->request->getPost('judul'),
            'versi_doc'  => $this->request->getPost('versi_doc') ?: '1.0',
            'content'    => $this->request->getPost('content'),
            'link_file'  => $this->request->getPost('link_file'),
            'updated_by' => session()->get('name') ?? session()->get('username')
        ]);

        return redirect()->to(base_url('software-engineer/dokumentasi-teknis/arsitektur-sistem'))->with('success', 'Catatan/Diagram Arsitektur berhasil disimpan.');
    }
}
