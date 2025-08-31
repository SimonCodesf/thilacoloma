<!DOCTYPE html>
<html>
<head>
    <title>Test Large File Upload</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .upload-area { 
            border: 2px dashed #ccc; 
            padding: 40px; 
            text-align: center; 
            margin: 20px 0;
            border-radius: 8px;
        }
        .upload-area:hover { border-color: #007cba; }
        input[type="file"] { margin: 10px; padding: 8px; }
        button { 
            background: #007cba; 
            color: white; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 4px; 
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background: #005a87; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .progress { 
            width: 100%; 
            height: 20px; 
            background: #f0f0f0; 
            margin: 10px 0; 
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-bar { 
            height: 100%; 
            background: linear-gradient(90deg, #007cba 0%, #00a0e6 100%); 
            width: 0%; 
            transition: width 0.3s ease;
        }
        .result { 
            margin-top: 20px; 
            padding: 15px; 
            background: #f9f9f9; 
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Large File Upload Test</h1>
    
    <div class="info">
        <strong>Testing PHP Upload Configuration</strong><br>
        This page tests if large file uploads work with our custom PHP configuration.<br>
        Try uploading your <code>kampthilala2025.pdf</code> file (7MB) to see if the 50MB limits are working.
    </div>
    
    <div class="upload-area">
        <input type="file" id="fileInput" accept=".pdf">
        <br>
        <button id="uploadBtn" onclick="uploadFile()">Upload File</button>
    </div>
    
    <div class="progress" id="progressContainer" style="display:none;">
        <div class="progress-bar" id="progressBar"></div>
        <div id="progressText" style="text-align: center; margin-top: 5px;">0%</div>
    </div>
    
    <div id="result" class="result" style="display:none;"></div>

    <script>
        function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            const uploadBtn = document.getElementById('uploadBtn');
            
            if (!file) {
                alert('Please select a file');
                return;
            }
            
            console.log('Starting upload for file:', file.name, 'Size:', file.size, 'bytes');
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            const xhr = new XMLHttpRequest();
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const progressContainer = document.getElementById('progressContainer');
            const resultDiv = document.getElementById('result');
            
            // Disable upload button
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
            
            // Show progress bar
            progressContainer.style.display = 'block';
            resultDiv.style.display = 'none';
            
            // Upload progress
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = percentComplete + '% (' + formatBytes(e.loaded) + ' / ' + formatBytes(e.total) + ')';
                    console.log('Upload progress:', percentComplete + '%');
                }
            });
            
            // Handle response
            xhr.addEventListener('load', function() {
                progressContainer.style.display = 'none';
                resultDiv.style.display = 'block';
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload File';
                
                console.log('Upload completed with status:', xhr.status);
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        resultDiv.className = 'result success';
                        resultDiv.innerHTML = '<h3>✅ Upload Success!</h3><pre>' + JSON.stringify(response, null, 2) + '</pre>';
                    } catch (e) {
                        resultDiv.className = 'result error';
                        resultDiv.innerHTML = '<h3>❌ Upload Failed (JSON Parse Error)</h3><p>Status: ' + xhr.status + '</p><p>Response: ' + xhr.responseText + '</p>';
                    }
                } else {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = '<h3>❌ Upload Failed</h3><p>Status: ' + xhr.status + ' ' + xhr.statusText + '</p><p>Response: ' + xhr.responseText + '</p>';
                }
            });
            
            // Handle errors
            xhr.addEventListener('error', function() {
                progressContainer.style.display = 'none';
                resultDiv.style.display = 'block';
                resultDiv.className = 'result error';
                resultDiv.innerHTML = '<h3>❌ Network Error</h3><p>Upload failed due to network error</p>';
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload File';
                console.error('Upload error: network error');
            });
            
            // Handle timeout
            xhr.addEventListener('timeout', function() {
                progressContainer.style.display = 'none';
                resultDiv.style.display = 'block';
                resultDiv.className = 'result error';
                resultDiv.innerHTML = '<h3>⏰ Upload Timeout</h3><p>Upload took too long (5 minutes timeout)</p>';
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload File';
                console.error('Upload error: timeout');
            });
            
            // Configure request
            xhr.timeout = 300000; // 5 minutes
            xhr.open('POST', '/debug/test-upload');
            
            console.log('Sending upload request...');
            xhr.send(formData);
        }
        
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
        
        // File selection feedback
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                console.log('File selected:', file.name, 'Size:', formatBytes(file.size), 'Type:', file.type);
            }
        });
    </script>
</body>
</html>
