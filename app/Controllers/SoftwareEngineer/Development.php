<?php

namespace App\Controllers\SoftwareEngineer;

use App\Models\SoftwareEngineer\SeTaskModel;
use App\Models\SoftwareEngineer\SeSystemModel;

class Development extends BaseSeController
{
    protected $taskModel;
    protected $systemModel;

    public function __construct()
    {
        $this->taskModel   = new SeTaskModel();
        $this->systemModel = new SeSystemModel();
    }

    public function taskBoard()
    {
        $data = [
            'title'   => 'Task Board Development - Software Engineer',
            'active'  => 'development',
            'sub'     => 'task-board',
            'tasks'   => $this->taskModel->getTasksWithDetail(),
            'systems' => $this->systemModel->findAll()
        ];

        return view('software_engineer/development/task_board', $data);
    }

    public function storeTask()
    {
        $this->taskModel->save([
            'system_id'        => $this->request->getPost('system_id') ?: null,
            'task_name'        => $this->request->getPost('task_name'),
            'deskripsi'        => $this->request->getPost('deskripsi'),
            'milestone_sprint' => $this->request->getPost('milestone_sprint'),
            'priority'         => $this->request->getPost('priority'),
            'status'           => $this->request->getPost('status') ?: 'todo',
            'due_date'         => $this->request->getPost('due_date') ?: null,
            'assigned_to'      => session()->get('name') ?? session()->get('username')
        ]);

        return redirect()->to(base_url('software-engineer/development/task-board'))->with('success', 'Task development berhasil ditambahkan.');
    }

    public function updateTaskStatus()
    {
        $taskId = $this->request->getPost('task_id');
        $status = $this->request->getPost('status');

        if ($taskId && $status) {
            $this->taskModel->update($taskId, ['status' => $status]);
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error'], 400);
    }

    public function deleteTask($id)
    {
        $this->taskModel->delete($id);
        return redirect()->to(base_url('software-engineer/development/task-board'))->with('success', 'Task berhasil dihapus.');
    }

    public function sprint()
    {
        $data = [
            'title'   => 'Timeline / Sprint - Software Engineer',
            'active'  => 'development',
            'sub'     => 'sprint',
            'tasks'   => $this->taskModel->getTasksWithDetail(),
            'systems' => $this->systemModel->findAll()
        ];

        return view('software_engineer/development/sprint', $data);
    }

    public function infoClient()
    {
        $db = \Config\Database::connect();
        
        $clients = [];
        if ($db->tableExists('client')) {
            $clients = $db->table('client')->get()->getResultArray();
        }

        $projects = [];
        if ($db->tableExists('projects')) {
            $projects = $db->table('projects')->get()->getResultArray();
        }

        $data = [
            'title'    => 'Info Client & Requirement Project - Software Engineer',
            'active'   => 'development',
            'sub'      => 'info-client',
            'clients'  => $clients,
            'projects' => $projects
        ];

        return view('software_engineer/development/info_client', $data);
    }
}
