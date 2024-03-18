<div>
    <x-slot name="title">User - Document</x-slot>
    <div class="container my-3">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($showTable == true)
            <div class="card my-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Document ( {{ $totalDocuments }} )</h3>
                        <button class="btn btn-success" wire:click='showForm'>
                            <span wire:loading.remove wire:target='showForm'>New</span>
                            <span wire:loading wire:target='showForm'>New....</span>
                        </button></div>
                </div>
            </div>
        @endif

        @if ($showTable == true)
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>User</th>
                                    <th>Creation date</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $document)
                                    <tr>
                                        <td>{{ $document->id }}</td>
                                        <td><a wire:click="viewDocument({{ $document->id }}, '{{ $document->file_path }}')" class="cursor-pointer text-blue-500">{{ $document->title }}</a></td>
                                        <td>{{ $document->users->fname . ' ' . $document->users->lname }}</td>
                                        <td>{{ $document->created_at }}</td>
                                        <td><button wire:click='edit({{ $document->id }})'
                                                class="btn btn-primary">Edit</button></td>
                                        <td><button wire:click='delete({{ $document->id }})'
                                                class="btn btn-danger">Delete</button></td>
                                    </tr>
                                @empty
                                    <h4>Document Not Found</h4>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $documents->links('custom-pagination-links-view') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($createForm == true)
            <div class="container">
                <button class="btn btn-success" wire:click='goback'>
                    <span wire:loading.remove wire:target='goback'>GoBack</span>
                    <span wire:loading wire:target='goback'>GoBack....</span>
                </button>
                <form wire:submit.prevent='create'>
                    <div class="form-group my-1">
                    @csrf
                    <label for="date">Titre</label>
                    <input type="text" wire:model='title' class="form-control">
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="author">Auteur</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            
           <!-- 
            <div class="form-group">
                <label for="type">Type de Document</label>
                <select class="form-control" id="type" name="file" required>
                    <option value="pdf">PDF</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fichier">Fichier</label>
                <input type="file" class="form-control-file" id="fichier" name="fichier" accept=".pdf, .docx, .mp4, .jpg, .png" required>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>--->
         
                    <div class="form-group my-1">
                        <label for="">Upload Document</label>
                        <input type="file" wire:model='file' class="form-control" accept=".pdf, image/*" required>
                       
                        @error('document')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type='submit' class="btn btn-success">Save</button>

                    

                </form>

            </div>
        @endif

        @if ($updateForm == true)
            <div class="container">
                <button class="btn btn-success" wire:click='goback'>
                    <span wire:loading.remove wire:target='goback'>GoBack</span>
                    <span wire:loading wire:target='goback'>GoBack....</span>
                </button>
                <form wire:submit.prevent='update({{ $edit_id }})'>
                    <div class="form-group my-1">
                    @csrf
                    <input type="text" wire:model='title' class="form-control">
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="auteur">Auteur</label>
                <input type="text" class="form-control" id="auteur" name="auteur" required>
            </div>
            <div class="form-group">
                <label for="type">Type de Document</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="pdf">PDF</option>
                    <option value="docx">DOCX</option>
                    <option value="mp4">MP4</option>
                    <option value="jpg">JPG</option>
                    <option value="png">PNG</option>
                </select>
            </div>
                    <div class="form-group my-1">
                        <label for="">Upload Document</label>
                        <input type="file" wire:model='new_document' class="form-control">
                        <input type="hidden" wire:model='old_document' class="form-control">
                        @if ($new_document)
                            <span>{{ $new_document }}</span>
                        @else
                            <span>{{ $old_document }}</span>
                        @endif
                        @error('document')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type='submit' class="btn btn-success">Save</button>
                </form>
            </div>
        @endif
    </div>

    @if ($selectedDocument)
        <!-- Section pour afficher les détails du document sélectionné -->
        <div>
            <h2>{{ $selectedDocument->title }}</h2>
            <p>Type: {{ $selectedDocument->document_type }}</p>
            <p>Date de création: {{ $selectedDocument->created_at }}</p>

            <!-- Ajoutez d'autres détails du document si nécessaire -->

            <a wire:click="closeDocumentView" class="cursor-pointer text-blue-500">Fermer</a>
        </div>
    @endif