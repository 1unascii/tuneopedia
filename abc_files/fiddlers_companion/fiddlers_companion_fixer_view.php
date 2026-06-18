<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fiddler's Companion Fixer</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #ccc; padding: 20px; max-width: 1200px; }
        h1 { color: #ddd; font-size: 1.2em; }
        button { padding: 8px 20px; font-size: 1em; cursor: pointer; margin-top: 10px; }
        .info { color: #6af; }
        .pass { color: #6c6; }
        .fail { color: #c66; }
        #results { white-space: pre-wrap; line-height: 1.8; margin-top: 20px; }
        table { border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px 12px; border: 1px solid #555; text-align: left; }
        th { background: rgba(255,255,255,0.05); }
    </style>
</head>
<body>
    <h1>Fiddler's Companion Fixer</h1>
    <p class="info">Processes all .abc files in this directory — strips annotations, extracts titles and AKA names, overwrites files in place.</p>

    <button id="fix-btn">Run Fixer</button>

    <div id="results"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
    $('#fix-btn').on('click', function () {
        $('#results').html('<div class="info">Processing...</div>');

        $.post('fiddlers_companion_fixer.php', {}, function (data) {
            var r = (typeof data === 'string') ? JSON.parse(data) : data;

            if (r.error) {
                $('#results').html('<div class="fail">Error: ' + r.error + '</div>');
                return;
            }

            var html = '<div class="pass">' + r.message + '</div>';
            html += '<div class="info">Total tunes: ' + r.total_tunes + '</div>';
            html += '<table><thead><tr><th>File</th><th>Tunes</th><th>Status</th></tr></thead><tbody>';
            for (var i = 0; i < r.files.length; i++) {
                var cls = r.files[i].status === 'fixed' ? 'pass' : 'fail';
                html += '<tr><td>' + r.files[i].file + '</td><td>' + r.files[i].tune_count + '</td><td class="' + cls + '">' + r.files[i].status + '</td></tr>';
            }
            html += '</tbody></table>';
            $('#results').html(html);
        }).fail(function (xhr) {
            $('#results').html('<div class="fail">Request failed: ' + (xhr.responseText || xhr.statusText) + '</div>');
        });
    });
    </script>
</body>
</html>
