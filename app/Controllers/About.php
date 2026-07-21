<?php
namespace App\Controllers;

class About extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'About Us - CDW Engineering',
            'active' => 'about'
        ];
        return view('templates/header', $data)
            . view('templates/nav')
            . view('about')
            . view('templates/footer');
    }
}