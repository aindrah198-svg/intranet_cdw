<?php

namespace App\Controllers\SoftwareEngineer;

use App\Models\SoftwareEngineer\SeBugModel;
use App\Models\SoftwareEngineer\SeMaintenanceModel;
use App\Models\SoftwareEngineer\SeBackupLogModel;
use App\Models\SoftwareEngineer\SeSystemModel;

class BugMaintenance extends BaseSeController
{
    protected $bugModel;
    protected $maintenanceModel;
    protected $backupModel;
    protected $systemModel;

    public function __construct()
    {
        $this->bugModel         = new SeBugModel();
        $this->maintenanceModel = new SeMaintenanceModel();
        $this->backupModel      = new SeBackupLogModel();
        $this->systemModel      = new SeSystemModel();
    }

    // Bug Tracking
    public function bugTracking()
    {
        $data = [
            'title'   => 'Bug Tracking - Software Engineer',
            'active'  => 'bug-maintenance',
            'sub'     => 'bug-tracking',
            'bugs'    => $this->bugModel->getBugsWithSystem(),
            'systems' => $this->systemModel->findAll()
        ];

        return view('software_engineer/bug_maintenance/bug_tracking', $data);
    }

    public function storeBug()
    {
        $this->bugModel->save([
            'system_id'     => $this->request->getPost('system_id'),
            'judul_bug'     => $this->request->getPost('judul_bug'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'severity'      => $this->request->getPost('severity') ?: 'medium',
            'status'        => $this->request->getPost('status') ?: 'open',
            'reporter'      => $this->request->getPost('reporter') ?: session()->get('name'),
            'assigned_to'   => $this->request->getPost('assigned_to') ?: session()->get('name'),
            'tgl_ditemukan' => $this->request->getPost('tgl_ditemukan') ?: date('Y-m-d')
        ]);

        return redirect()->to(base_url('software-engineer/bug-maintenance/bug-tracking'))->with('success', 'Bug report berhasil dibuat.');
    }

    public function updateBugStatus($id)
    {
        $status = $this->request->getPost('status');
        $solusi = $this->request->getPost('solusi');

        $updateData = ['status' => $status];
        if ($solusi) {
            $updateData['solusi'] = $solusi;
        }
        if (in_array($status, ['fixed', 'verified', 'closed'])) {
            $updateData['tgl_diselesaikan'] = date('Y-m-d');
        }

        $this->bugModel->update($id, $updateData);
        return redirect()->to(base_url('software-engineer/bug-maintenance/bug-tracking'))->with('success', 'Status bug berhasil diperbarui.');
    }

    // Maintenance Terjadwal
    public function maintenanceTerjadwal()
    {
        $data = [
            'title'      => 'Maintenance Terjadwal - Software Engineer',
            'active'     => 'bug-maintenance',
            'sub'        => 'maintenance-terjadwal',
            'schedules'  => $this->maintenanceModel->getSchedulesWithSystem(),
            'systems'    => $this->systemModel->findAll()
        ];

        return view('software_engineer/bug_maintenance/maintenance_terjadwal', $data);
    }

    public function storeMaintenance()
    {
        $this->maintenanceModel->save([
            'system_id'         => $this->request->getPost('system_id'),
            'judul_maintenance' => $this->request->getPost('judul_maintenance'),
            'jenis_maintenance' => $this->request->getPost('jenis_maintenance'),
            'tgl_rencana'       => $this->request->getPost('tgl_rencana'),
            'estimasi_downtime' => $this->request->getPost('estimasi_downtime'),
            'status'            => $this->request->getPost('status') ?: 'terjadwal',
            'penanggung_jawab'  => $this->request->getPost('penanggung_jawab') ?: session()->get('name'),
            'catatan'           => $this->request->getPost('catatan')
        ]);

        return redirect()->to(base_url('software-engineer/bug-maintenance/maintenance-terjadwal'))->with('success', 'Jadwal maintenance berhasil disimpan.');
    }

    // Backup Log
    public function backupLog()
    {
        $data = [
            'title'   => 'Backup Log System - Software Engineer',
            'active'  => 'bug-maintenance',
            'sub'     => 'backup-log',
            'backups' => $this->backupModel->getLogsWithSystem(),
            'systems' => $this->systemModel->findAll()
        ];

        return view('software_engineer/bug_maintenance/backup_log', $data);
    }

    public function storeBackup()
    {
        $this->backupModel->save([
            'system_id'      => $this->request->getPost('system_id'),
            'jenis_backup'   => $this->request->getPost('jenis_backup'),
            'tanggal_backup' => $this->request->getPost('tanggal_backup') ?: date('Y-m-d H:i:s'),
            'ukuran_mb'      => $this->request->getPost('ukuran_mb') ?: 0,
            'lokasi_simpan'  => $this->request->getPost('lokasi_simpan'),
            'status_backup'  => $this->request->getPost('status_backup') ?: 'sukses',
            'petugas'        => session()->get('name') ?? session()->get('username'),
            'catatan'        => $this->request->getPost('catatan')
        ]);

        return redirect()->to(base_url('software-engineer/bug-maintenance/backup-log'))->with('success', 'Catatan backup berhasil disimpan.');
    }
}
