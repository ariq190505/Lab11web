<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX Demo - Lab11Web</title>
    <link rel="stylesheet" href="<?= base_url('/style.css') ?>">
    <style>
        .ajax-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .ajax-controls {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .btn-group {
            margin-bottom: 15px;
        }
        
        .btn {
            padding: 8px 16px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        
        .btn:hover { opacity: 0.8; }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .alert {
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover { color: black; }
    </style>
</head>
<body>
    <div class="ajax-container">
        <h1>🚀 AJAX Demo - Lab11Web</h1>
        <p>Demonstrasi operasi CRUD menggunakan AJAX dengan jQuery</p>
        
        <!-- Alert Messages -->
        <div id="alertContainer"></div>
        
        <!-- Controls -->
        <div class="ajax-controls">
            <div class="btn-group">
                <button class="btn btn-primary" onclick="loadData()">📊 Load Data</button>
                <button class="btn btn-success" onclick="showAddModal()">➕ Add Article</button>
                <button class="btn btn-warning" onclick="clearData()">🗑️ Clear Display</button>
            </div>
            
            <div class="btn-group">
                <strong>Quick Actions:</strong>
                <button class="btn btn-primary" onclick="loadSpecificData(1)">Load ID #1</button>
                <button class="btn btn-primary" onclick="loadSpecificData(2)">Load ID #2</button>
                <button class="btn btn-danger" onclick="deleteData(1)">Delete ID #1</button>
            </div>
        </div>
        
        <!-- Data Display -->
        <div id="dataContainer">
            <div class="loading">Click "Load Data" to fetch articles via AJAX</div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="articleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Article</h2>
            <form id="articleForm">
                <input type="hidden" id="articleId" value="">
                <div class="form-group">
                    <label for="judul">Title:</label>
                    <input type="text" id="judul" name="judul" required>
                </div>
                <div class="form-group">
                    <label for="isi">Content:</label>
                    <textarea id="isi" name="isi" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="0">Draft</option>
                        <option value="1">Published</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">💾 Save</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">❌ Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery Library -->
    <script src="<?= base_url('/assets/js/jquery-3.6.0.min.js') ?>"></script>
    
    <!-- AJAX Scripts -->
    <script>
        // Base URL for AJAX calls
        const baseUrl = '<?= base_url() ?>';
        
        // Load all data
        function loadData() {
            showLoading();
            
            $.ajax({
                url: baseUrl + '/ajax/getData',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    displayData(data);
                    showAlert('Data loaded successfully!', 'success');
                },
                error: function(xhr, status, error) {
                    showAlert('Error loading data: ' + error, 'error');
                    $('#dataContainer').html('<div class="loading">Error loading data</div>');
                }
            });
        }
        
        // Load specific data by ID
        function loadSpecificData(id) {
            showLoading();
            
            $.ajax({
                url: baseUrl + '/ajax/getById/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'OK') {
                        displayData([response.data]);
                        showAlert('Data ID #' + id + ' loaded successfully!', 'success');
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('Error loading data: ' + error, 'error');
                }
            });
        }
        
        // Delete data
        function deleteData(id) {
            if (!confirm('Are you sure you want to delete this article?')) {
                return;
            }
            
            $.ajax({
                url: baseUrl + '/ajax/delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'OK') {
                        showAlert(response.message, 'success');
                        loadData(); // Reload data
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('Error deleting data: ' + error, 'error');
                }
            });
        }
        
        // Display data in table
        function displayData(data) {
            let html = '<table class="data-table">';
            html += '<thead><tr><th>ID</th><th>Title</th><th>Content</th><th>Status</th><th>Actions</th></tr></thead>';
            html += '<tbody>';
            
            if (data && data.length > 0) {
                data.forEach(function(item) {
                    html += '<tr>';
                    html += '<td>' + item.id + '</td>';
                    html += '<td>' + item.judul + '</td>';
                    html += '<td>' + (item.isi ? item.isi.substring(0, 100) + '...' : '') + '</td>';
                    html += '<td>' + (item.status == 1 ? 'Published' : 'Draft') + '</td>';
                    html += '<td>';
                    html += '<button class="btn btn-warning" onclick="editData(' + item.id + ')">Edit</button> ';
                    html += '<button class="btn btn-danger" onclick="deleteData(' + item.id + ')">Delete</button>';
                    html += '</td>';
                    html += '</tr>';
                });
            } else {
                html += '<tr><td colspan="5" style="text-align: center;">No data found</td></tr>';
            }
            
            html += '</tbody></table>';
            $('#dataContainer').html(html);
        }
        
        // Show loading
        function showLoading() {
            $('#dataContainer').html('<div class="loading">Loading data...</div>');
        }
        
        // Clear data display
        function clearData() {
            $('#dataContainer').html('<div class="loading">Data cleared. Click "Load Data" to fetch articles.</div>');
            showAlert('Display cleared', 'success');
        }
        
        // Show alert message
        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            const alertHtml = '<div class="alert ' + alertClass + '">' + message + '</div>';
            $('#alertContainer').html(alertHtml);
            
            // Auto hide after 3 seconds
            setTimeout(function() {
                $('#alertContainer').html('');
            }, 3000);
        }
        
        // Modal functions
        function showAddModal() {
            $('#modalTitle').text('Add New Article');
            $('#articleForm')[0].reset();
            $('#articleId').val('');
            $('#articleModal').show();
        }
        
        function editData(id) {
            // Load data for editing
            $.ajax({
                url: baseUrl + '/ajax/getById/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'OK') {
                        $('#modalTitle').text('Edit Article');
                        $('#articleId').val(response.data.id);
                        $('#judul').val(response.data.judul);
                        $('#isi').val(response.data.isi);
                        $('#status').val(response.data.status);
                        $('#articleModal').show();
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('Error loading data for edit: ' + error, 'error');
                }
            });
        }
        
        function closeModal() {
            $('#articleModal').hide();
        }
        
        // Form submission
        $('#articleForm').submit(function(e) {
            e.preventDefault();
            
            const id = $('#articleId').val();
            const url = id ? baseUrl + '/ajax/update/' + id : baseUrl + '/ajax/create';
            const method = 'POST';
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'OK') {
                        showAlert(response.message, 'success');
                        closeModal();
                        loadData(); // Reload data
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('Error saving data: ' + error, 'error');
                }
            });
        });
        
        // Close modal when clicking outside
        $(window).click(function(event) {
            if (event.target.id === 'articleModal') {
                closeModal();
            }
        });
        
        // Load data on page load
        $(document).ready(function() {
            console.log('AJAX Demo Page Loaded');
            console.log('jQuery version:', $.fn.jquery);
        });
    </script>
</body>
</html>
