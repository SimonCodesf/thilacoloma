<!DOCTYPE html>
<html>
<head>
    <title>Test Large File Upload</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .upload-area { border: 2px dashed #ccc; padding: 40px; text-align: center; }
        .progress { width: 100%; height: 20px; background: #f0f0f0; margin: 10px 0; }
        .progress-bar { height: 100%; background: #007cba; width: 0%; }
        .result { margin-top: 20px; padding: 10px; background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Large File Upload Test</h1>
    
    <div class="upload-area">
        <input type="file" id="fileInput" accept=".pdf">
        <button onclick="uploadFile()">Upload File</button>
    </div>
    
    <div class="progress" style="display:none;">
        <div class="progress-bar" id="progressBar"></div>
    </div>
    
    <div id="result" class="result" style="display:none;"></div>

    <script>
        function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Please select a file');
                return;
            }
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            const xhr = new XMLHttpRequest();
            const progressBar = document.getElementById('progressBar');
            const resultDiv = document.getElementById('result');
            
            // Show progress bar
            document.querySelector('.progress').style.display = 'block';
            resultDiv.style.display = 'none';
            
            // Upload progress
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                }
            });
            
            // Handle response
            xhr.addEventListener('load', function() {
                document.querySelector('.progress').style.display = 'none';
                resultDiv.style.display = 'block';
                
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    resultDiv.innerHTML = '<h3>Upload Success!</h3><pre>' + JSON.stringify(response, null, 2) + '</pre>';
                    resultDiv.style.background = '#d4edda';
                } else {
                    resultDiv.innerHTML = '<h3>Upload Failed</h3><p>Status: ' + xhr.status + '</p><p>Response: ' + xhr.responseText + '</p>';
                    resultDiv.style.background = '#f8d7da';
                }
            });
            
            // Handle errors
            xhr.addEventListener('error', function() {
                document.querySelector('.progress').style.display = 'none';
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<h3>Upload Error</h3><p>Network error occurred</p>';
                resultDiv.style.background = '#f8d7da';
            });
            
            // Handle timeout
            xhr.addEventListener('timeout', function() {
                document.querySelector('.progress').style.display = 'none';
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<h3>Upload Timeout</h3><p>Upload took too long</p>';
                resultDiv.style.background = '#f8d7da';
            });
            
            // Configure request
            xhr.timeout = 300000; // 5 minutes
            xhr.open('POST', '/debug/test-upload');
            xhr.send(formData);
        }
    </script>
</body>
</html>
