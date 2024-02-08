<?php

namespace App\Http\Livewire\User;


use PDF;
use App\Models\Document as ModelsDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
//use Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;


class Document extends Component
{
    public $title,
        $file,
        $edit_id,
        $edit_title,
        $new_file,
        $old_file,
        $showTable = true,
        $createForm = false,
        $updateForm = false,
        $isCameraOpen = false;
    public $totalDocuments;
    public $new_document;
    public $old_document;
    public $selectedDocument;


    use WithFileUploads;
    use WithPagination;

    public function pageinationView()
    {
        return 'custom-pagination-links-view';
    }

    public function viewDocument($documentId)
    {
        $this->selectedDocument = ModelsDocument::findOrFail($documentId);
        $this->showTable = false;
        $this->createForm = false;
        $this->updateForm = false;
    }

    public function closeDocumentView()
    {
        $this->selectedDocument = null;
        $this->showTable = true;
    }

    public function render()
{
    $documents = ModelsDocument::orderBy('id', 'DESC')->where('user_id', Auth::user()->id)->paginate(4);
    $this->totalDocuments = ModelsDocument::count();
    
    return view('livewire.user.document', [
        'documents' => $documents,
        'new_document' => $this->new_document,
        'old_document' => $this->old_document,
        'selectedDocument' => $this->selectedDocument,
    ])->layout('layouts.user-app');
}


    public function goback()
    {
        $this->showTable = true;
        $this->createForm = false;
        $this->updateForm = false;
    }

    public function showForm()
    {
        $this->resetValidation();
        $this->reset(['title', 'file']);
        $this->showTable = false;
        $this->createForm = true;
    }

    public function create()
    {
        $document = new ModelsDocument();
        $this->validate([
            'title' => ['required'],
            'file' => ['required', 'mimes:pdf,jpg, png', 'max:20480'], // Adjust the mime types and max size accordingly
            
        ]);

        $filename = "";

        if ($this->file && $this->file->getMimeType() == 'image/*') {
            
            $pdfFilename = 'documents/' . Str::uuid() . '.pdf';
            $image = Image::make($this->file)->encode('pdf');
            $image->save(storage_path("app/public/{$pdfFilename}"));

            $this->file = $pdfFilename;
        } else {
            // Si le fichier n'est pas une image, stocke-le normalement
            $document->document_type = 'pdf'; // Ajoute un champ pour spécifier le type de document
            $document->file_path = Storage::putFile('documents', $this->file, 'public');
            
        }

        $document->title = $this->title;
        $document->user_id = Auth::user()->id;
        $document->document_type = 'pdf';
        $document->file_path = $this->file;
        $result = $document->save();

        if ($result) {
            session()->flash('success', 'Document uploaded successfully');
            $this->title = "";
            $this->file = "";
            $this->goBack();
        }
    }

    public function edit($id)
    {
        $this->showTable = false;
        $this->updateForm = true;

        $document = ModelsDocument::findOrFail($id);

        $this->edit_id = $document->id;
        $this->edit_title = $document->title;
        $this->old_file = $document->file_path;
    }

    public function update($id)
    {
        $document = ModelsDocument::findOrFail($id);

        $this->validate([
            'edit_title' => ['required'],
            'new_file' => ['nullable', 'mimes:pdf,jpeg,png', 'max:20480'], // Adjust the mime types and max size accordingly
        ]);

        $filename = "";

        // If a new file is uploaded, handle it
        if ($this->new_file) {
            // Delete the old file
            $path = public_path('storage/') . $document->file_path;
            if (File::exists($path)) {
                File::delete($path);
            }

            // Save the new file
            if ($this->new_file->getMimeType() == 'image/jpeg' || $this->new_file->getMimeType() == 'image/png') {
                $image = Image::make($this->new_file);
                $filename = 'documents/' . uniqid() . '.pdf';
                $image->save(storage_path('app/public/' . $filename));
            } else {
                $filename = $this->new_file->store('documents', 'public');
            }
        } else {
            // Keep the existing file
            $filename = $this->old_file;
        }

        $document->title = $this->edit_title;
        $document->file_path = $filename;
        $result = $document->save();

        if ($result) {
            session()->flash('success', 'Document updated successfully');
            $this->edit_title = "";
            $this->new_file = "";
            $this->old_file = "";
            $this->goBack();
        }
    }
    public function openCamera()
    {
        $this->isCameraOpen = true;
    }

    public function takePhoto()
    {
        // Logique pour prendre la photo
    }

    public function closeCamera()
    {
        $this->isCameraOpen = false;
    }
}
