<x-layout.user-app>
    <x-slot name="title">User - Documents</x-slot>

    <div class="container mt-4">
        <h2>Liste des Documents</h2>
        <ul class="list-group">
            @forelse ($documents as $document)
                <li class="list-group-item">
                    <a href="{{ route('documents.show', $document) }}">{{ $document->titre }}</a>
                    <span class="badge badge-primary">{{ $document->date }}</span>
                    <span class="badge badge-secondary">{{ $document->auteur }}</span>
                    <!-- Ajoute d'autres informations d'indexation ici -->
                </li>
            @empty
                <li class="list-group-item">Aucun document disponible.</li>
            @endforelse
        </ul>
    </div>
</x-layout.user-app>
