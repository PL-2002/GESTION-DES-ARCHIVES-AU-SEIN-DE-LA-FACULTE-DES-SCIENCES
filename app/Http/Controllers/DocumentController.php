use App\Models\Document;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;


public function index()
{
    $documents = Document::all();
    return view('documents.index', compact('documents'));
}

public function create()
{
    return view('documents.create');
}

public function store(Request $request)
{
    $request->validate([
        'titre' => 'required',
        'date' => 'required|date',
        'auteur' => 'required',
        'type' => 'required|in:pdf,docx,mp4,jpg,png',
        'fichier' => 'required|file|mimes:pdf,docx,mp4,jpg,png|max:10240', // Taille maximale de 10 Mo
    ]);
    // Configuration de l'API Google Drive
    $client = new Google_Client();
    $client->setAuthConfig('client_secret_632025818580-gst8optjikm150ulfkcpglugdab3figb.apps.googleusercontent.com.json'); 
    $client->setScopes([Google_Service_Drive::DRIVE]);
    $client->setAccessType('offline');
    $service = new Google_Service_Drive($client);

    // Upload du fichier sur Google Drive
    $fichier = $request->file('fichier');
    $driveFile = new Google_Service_Drive_DriveFile([
        'name' => $request->titre,
        'parents' => ['rchiv-2002'], // ID du dossier sur Google Drive
    ]);
    $driveFile = $service->files->create($driveFile, [
        'data' => file_get_contents($fichier->path()),
        'mimeType' => $fichier->getMimeType(),
    ]);

    // Enregistrement dans la base de données
    Document::create([
        'titre' => $request->titre,
        'date' => $request->date,
        'auteur' => $request->auteur,
        'type' => $request->type,
        'chemin_fichier' => $driveFile->id, // 
    ]);

    return redirect()->route('documents.index')->with('success', 'Document ajouté avec succès.');
}



public function show(Document $document)
{
    return view('documents.show', compact('document'));
}

public function edit(Document $document)
{
    return view('documents.edit', compact('document'));
}

public function update(Request $request, Document $document)
{
    // Logique de mise à jour des documents
}

public function destroy(Document $document)
{
    // Logique de suppression des documents
}

public function annotate(Document $document)
{
    return view('documents.annotate', compact('document'));
}

public function storeAnnotation(Request $request, Document $document)
{
    $request->validate([
        'commentaire' => 'required',
        // Ajoute d'autres règles de validation pour les champs d'annotation si nécessaire
    ]);

    // Enregistre l'annotation dans la base de données
    $document->annotations()->create([
        'commentaire' => $request->commentaire,
        // Enregistre d'autres champs d'annotation si nécessaire
    ]);

    return redirect()->route('documents.show', $document)->with('success', 'Annotation ajoutée avec succès.');
}
