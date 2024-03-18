<?php

namespace App\Http\Livewire\User;

use Ilovepdf\Ilovepdf;
use PDF;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
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
    public $author;
    public $title,
        $date,
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
        $filePath = $this->selectedDocument->file_path;
        $this->downloadFile = $this->downloadPDF($filePath, $documentId);
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
            'file' => ['required', 'mimes:pdf,jpg,png', 'max:20480'],
            'author' => ['nullable'],
        ]);

        $document->title = $this->title;
        $document->author = $this->author;
        $document->user_id = Auth::user()->id;

        // Enregistrez l'image
        $document->document_type = 'pdf';
        $imagePath = $this->file->store('documents', 'public');
        $document->file_path = $imagePath;

        // Sauvegardez le document
        $result = $document->save();

        //if ($result) {
            // Appel à la méthode downloadPDF pour générer et télécharger le PDF
            //$this->downloadPDF($document->file_path, $document->id);
            //session()->flash('success', 'Document uploaded successfully');
            $this->title = "";
            $this->file = "";
            $this->goback();
        //}
    }

   
    public function downloadPDF($imagePath, $documentId)
   {
        //$imagePath = $documentId;
        // Initialisez l'API iLovePDF avec vos clés d'API (remplacez par vos propres clés)
        $ilovepdf = new Ilovepdf('project_public_194707eee8c6bd80b6e1ff62702bcbe0_3d2wMd011adc7ecb22574c9c081e9f3552ade', 'secret_key_0b46389dbf04b99020e9d5b5a4a92162_5CkIwf291d5e2527adf6c45503ec294200819');

        // Créez une tâche de conversion d'image en PDF
        $task = $ilovepdf->newTask('imagepdf');

        // Ajoutez le fichier image à la tâche
        $task->addFile(storage_path("app/public/$imagePath"));

        // Exécutez la tâche
        //$task->execute();
        $result = $task->execute(true);
         // Vérifiez si la tâche a réussi
        if (empty($result->error)) {
            // Téléchargez le fichier PDF résultant
        $pdfPath = storage_path("app/public/documents/{$documentId}.pdf");
        $result->download($pdfPath);
        } else {
            // Gérez l'erreur (ex. journalisation, affichage d'un message d'erreur)
            Log::error('Erreur lors de la conversion d\'image en PDF avec iLovePDF', ['error' => $result->getError()]);
            // Affichez un message d'erreur à l'utilisateur si nécessaire
        }

        // Téléchargez le fichier PDF résultant
        //$pdfPath = storage_path("app/public/documents/{$documentId}");
        //if (!file_exists(dirname($pdfPath))) {
            //mkdir(dirname($pdfPath), 0755, true);
        //}
        //$task->download($pdfPath);
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

    public function delete($id)
    {   
        $document = ModelsDocument::findOrFail($id);
        if ($document) {
            $document->delete();
            $this->emit('documentDeleted', $id);
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
