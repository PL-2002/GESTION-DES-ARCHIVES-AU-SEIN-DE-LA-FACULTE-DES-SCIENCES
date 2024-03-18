<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate PDF</title>
</head>
<body>
    @foreach($documents as $document)
        <img src="{{ asset('storage/' . $document->pdf_path) }}" alt="img" width=100%>
        <br>
    @endforeach
</body>
</html>
