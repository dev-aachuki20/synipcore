<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Logs</title>
    <style>
        body {
            font-family: monospace;
            white-space: pre-wrap;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .log-container {
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            overflow-x: auto;
            height: 600px;
            width: 100%;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <h1>Laravel Logs</h1>
    <div class="log-container">
        {!! $logs !!}
    </div>
</body>
</html>
