<?php

namespace App\Http\Livewire\User;

use PDF;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function downloadPDF()
    {
        $posts = Documents::all();
        $data = [
            'title' => '',
            'date' => date('d/m/Y'),
            'posts' => $posts
        ];

        $pdf = PDF::loadView('postpdf', $data);
        return $pdf->download('post.pdf');
    }
}
