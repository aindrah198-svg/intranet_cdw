<?php

namespace App\Controllers;

class Contact extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Contact Us | CDW Engineering',
            'active' => 'contact'
        ];
        
        return view('templates/header', $data)
            . view('templates/nav')
            . view('contact')
            . view('templates/footer');
    }
    
    public function send()
    {
        // Validation rules
        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Full name is required',
                    'min_length' => 'Name must be at least 3 characters',
                    'max_length' => 'Name cannot exceed 100 characters'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|max_length[100]',
                'errors' => [
                    'required' => 'Email address is required',
                    'valid_email' => 'Please enter a valid email address',
                    'max_length' => 'Email cannot exceed 100 characters'
                ]
            ],
            'subject' => [
                'rules' => 'required|in_list[General Inquiry,Engineering Services,Construction Project,Product Inquiry,Partnership,Career Opportunity,Other]',
                'errors' => [
                    'required' => 'Please select a subject',
                    'in_list' => 'Please select a valid subject'
                ]
            ],
            'message' => [
                'rules' => 'required|min_length[10]|max_length[2000]',
                'errors' => [
                    'required' => 'Message is required',
                    'min_length' => 'Message must be at least 10 characters',
                    'max_length' => 'Message cannot exceed 2000 characters'
                ]
            ],
            'company' => [
                'rules' => 'max_length[100]',
                'errors' => [
                    'max_length' => 'Company name cannot exceed 100 characters'
                ]
            ],
            'phone' => [
                'rules' => 'max_length[20]',
                'errors' => [
                    'max_length' => 'Phone number cannot exceed 20 characters'
                ]
            ]
        ];
        
        // Validate input
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Get form data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'company' => $this->request->getPost('company') ?? '',
            'phone' => $this->request->getPost('phone') ?? '',
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // TODO: Implement email sending or database storage here
        // Example: Save to database
        // $this->saveContactMessage($data);
        
        // Example: Send email
        // $this->sendEmail($data);
        
        // For now, just show success message
        return redirect()->to('/contact')->with('success', 
            'Thank you for your message! We will get back to you within 24 hours.'
        );
    }
    
    /**
     * Save contact message to database (optional)
     */
    private function saveContactMessage($data)
    {
        // Create contact_messages table first if not exists
        /*
        CREATE TABLE contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            company VARCHAR(100),
            phone VARCHAR(20),
            subject VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        */
        
        // Uncomment to enable database storage
        /*
        $db = \Config\Database::connect();
        $builder = $db->table('contact_messages');
        $builder->insert($data);
        */
    }
    
    /**
     * Send email notification (optional)
     */
    private function sendEmail($data)
    {
        $email = \Config\Services::email();
        
        $email->setFrom($data['email'], $data['name']);
        $email->setTo('info@cdw-engineering.co.id');
        $email->setCC('sales@cdw-engineering.co.id');
        $email->setSubject('New Contact Form: ' . $data['subject']);
        
        $message = "New contact form submission:\n\n";
        $message .= "Name: {$data['name']}\n";
        $message .= "Email: {$data['email']}\n";
        $message .= "Company: {$data['company']}\n";
        $message .= "Phone: {$data['phone']}\n";
        $message .= "Subject: {$data['subject']}\n\n";
        $message .= "Message:\n{$data['message']}\n\n";
        $message .= "IP Address: {$data['ip_address']}\n";
        $message .= "Sent from: {$data['user_agent']}\n";
        
        $email->setMessage($message);
        
        // Send email
        // $email->send();
    }
}