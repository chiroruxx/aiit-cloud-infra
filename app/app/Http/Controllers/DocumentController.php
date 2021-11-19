<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __invoke()
    {
        $html = file_get_contents(resource_path('views/doc.html'));
        return $html;
    }
}
