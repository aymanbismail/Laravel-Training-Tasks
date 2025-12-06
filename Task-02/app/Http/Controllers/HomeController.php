<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $title = "Welcome to my Laravel MVC demo!";
        return view('home', compact('title'));
    }

    public function about()
    {
        $info = "This is a simple about page demonstrating passing a string from the controller to a Blade view.";
        return view('about', compact('info'));
    }

    public function features()
    {
        // Indexed array of features
        $features = [
            "Eloquent ORM",
            "Blade Templating",
            "Routing",
            "Artisan CLI",
            "Migrations & Seeders"
        ];
        return view('features', compact('features'));
    }

    public function team()
    {
        // Associative array (each member is an associative array with name and role)
        $team = [
            ['name' => 'Alice Johnson', 'role' => 'Project Manager'],
            ['name' => 'Bob Smith', 'role' => 'Backend Developer'],
            ['name' => 'Charlie Davis', 'role' => 'Frontend Developer'],
            ['name' => 'Dana Lee', 'role' => 'QA Engineer'],
        ];
        return view('team', compact('team'));
    }
}
