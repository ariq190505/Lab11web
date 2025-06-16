<?= $this->include('template/header'); ?>

<style>
    /* Clean & Elegant Design */
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #f8f9fa;
        color: #2c3e50;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Simple Table Design */
    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
        margin: 20px 0;
    }

    .table-data {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .table-data thead th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        padding: 20px 15px;
        text-align: left;
        border-bottom: 2px solid #e9ecef;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-data tbody td {
        padding: 18px 15px;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }

    .table-data tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table-data tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Minimal Button Design */
    .btn {
        display: inline-block;
        padding: 6px 12px;
        margin: 0 4px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid transparent;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background: #0056b3;
        border-color: #0056b3;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background: #c82333;
        border-color: #c82333;
    }

    .btn-success {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }

    .btn-success:hover {
        background: #218838;
        border-color: #218838;
    }

    /* Clean Status Design */
    .status {
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
    }

    .status-published {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-draft {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .loading {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        font-style: italic;
        font-size: 16px;
    }
    
    .loading::before {
        content: "⏳ ";
        font-size: 20px;
    }
    
    /* Clean Actions Bar */
    .actions-bar {
        background: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }

    .actions-bar h2 {
        margin: 0;
        color: #2c3e50;
        font-size: 24px;
        font-weight: 300;
    }

    .btn-group {
        display: flex;
        gap: 8px;
    }

    /* Simple ID Display */
    .id-number {
        font-weight: 600;
        color: #6c757d;
        font-size: 13px;
    }

    /* Clean Loading State */
    .loading {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        font-size: 14px;
    }

    .loading::before {
        content: "●";
        display: inline-block;
        animation: loading 1.4s infinite both;
        margin-right: 8px;
    }

    @keyframes loading {
        0%, 80%, 100% { opacity: 0; }
        40% { opacity: 1; }
    }

    .alert {
        padding: 15px;
        margin: 15px 0;
        border: 1px solid transparent;
        border-radius: 6px;
        font-weight: 500;
    }
    
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ID Badge Styling */
    .id-badge {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 11px;
        display: inline-block;
        min-width: 30px;
        text-align: center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .table-container {
            margin: 10px -15px;
            border-radius: 0;
        }

        .table-data th,
        .table-data td {
            padding: 12px 8px;
            font-size: 12px;
        }

        .btn {
            padding: 6px 10px;
            font-size: 11px;
            margin: 1px 2px;
        }

        .actions-bar {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .btn-group {
            justify-content: center;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 480px) {
        .table-data {
            font-size: 11px;
        }

        .table-data th,
        .table-data td {
            padding: 8px 4px;
        }

        .btn {
            display: block;
            margin: 2px 0;
            width: 100%;
        }

        .actions-bar h2 {
            font-size: 20px;
        }
    }
</style>

<div class="actions-bar">
    <div>
        <h2>📊 Data Artikel</h2>
    </div>
    <div class="btn-group">
        <button class="btn btn-success" onclick="loadData()">Refresh</button>
        <button class="btn btn-primary" onclick="loadAllData()">Load All</button>
        <a href="<?= base_url('admin/artikel/add') ?>" class="btn btn-primary">Add Article</a>
    </div>
</div>

<!-- Alert Container -->
<div id="alertContainer"></div>

<!-- Data Table -->
<div class="table-container">
    <table class="table-data" id="artikelTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul Artikel</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" class="loading">Loading data...</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- jQuery Library -->
<script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>

<script>
$(document).ready(function() {
    console.log('AJAX Artikel Data Page Loaded');
    
    // Function to display a loading message while data is fetched
    function showLoadingMessage() {
        $('#artikelTable tbody').html('<tr><td colspan="5" class="loading">Loading data...</td></tr>');
    }
    
    // Function to show alert messages
    function showAlert(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `<div class="alert ${alertClass} fade-in">${message}</div>`;
        $('#alertContainer').html(alertHtml);
        
        // Auto hide after 4 seconds
        setTimeout(function() {
            $('#alertContainer').fadeOut(500, function() {
                $(this).html('').show();
            });
        }, 4000);
    }
    
    // Main function to load data
    function loadData() {
        showLoadingMessage();
        
        // AJAX request to getData endpoint
        $.ajax({
            url: "<?= base_url('ajax/getData') ?>",
            method: "GET",
            dataType: "json",
            success: function(data) {
                console.log('Data received:', data);
                
                // Build table rows
                var tableBody = "";
                
                if (data && data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        var statusClass = row.status == 1 ? 'status-published' : 'status-draft';
                        var statusText = row.status == 1 ? 'Published' : 'Draft';
                        var createdDate = row.created_at ? new Date(row.created_at).toLocaleDateString('id-ID') : '-';
                        
                        tableBody += '<tr>';
                        tableBody += '<td><span class="id-number">' + row.id + '</span></td>';
                        tableBody += '<td>' + (row.judul || 'No Title') + '</td>';
                        tableBody += '<td><span class="status ' + statusClass + '">' + statusText + '</span></td>';
                        tableBody += '<td>' + createdDate + '</td>';
                        tableBody += '<td>';
                        tableBody += '<a href="<?= base_url('artikel/') ?>' + (row.slug || row.id) + '" class="btn btn-primary">View</a>';
                        tableBody += '<a href="<?= base_url('admin/artikel/edit/') ?>' + row.id + '" class="btn btn-primary">Edit</a>';
                        tableBody += '<a href="#" class="btn btn-danger btn-delete" data-id="' + row.id + '">Delete</a>';
                        tableBody += '</td>';
                        tableBody += '</tr>';
                    }
                } else {
                    tableBody = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #6c757d;">📝 Tidak ada data artikel</td></tr>';
                }
                
                $('#artikelTable tbody').html(tableBody);
                showAlert(`✅ Data berhasil dimuat! Total: ${data.length} artikel`, 'success');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                
                $('#artikelTable tbody').html(
                    '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #dc3545;">' +
                    '❌ Error loading data: ' + error + 
                    '</td></tr>'
                );
                showAlert('❌ Gagal memuat data: ' + error, 'danger');
            }
        });
    }
    
    // Function to load all data
    function loadAllData() {
        showLoadingMessage();

        // AJAX request to getAllData endpoint
        $.ajax({
            url: "<?= base_url('ajax/getAllData') ?>",
            method: "GET",
            dataType: "json",
            success: function(data) {
                console.log('All data received:', data);

                // Build table rows (same logic as loadData)
                var tableBody = "";

                if (data && data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        var statusClass = row.status == 1 ? 'status-published' : 'status-draft';
                        var statusText = row.status == 1 ? 'Published' : 'Draft';
                        var createdDate = row.created_at ? new Date(row.created_at).toLocaleDateString('id-ID') : '-';

                        tableBody += '<tr>';
                        tableBody += '<td><span class="id-number">' + row.id + '</span></td>';
                        tableBody += '<td>' + (row.judul || 'No Title') + '</td>';
                        tableBody += '<td><span class="status ' + statusClass + '">' + statusText + '</span></td>';
                        tableBody += '<td>' + createdDate + '</td>';
                        tableBody += '<td>';
                        tableBody += '<a href="<?= base_url('artikel/') ?>' + (row.slug || row.id) + '" class="btn btn-primary">View</a>';
                        tableBody += '<a href="<?= base_url('admin/artikel/edit/') ?>' + row.id + '" class="btn btn-primary">Edit</a>';
                        tableBody += '<a href="#" class="btn btn-danger btn-delete" data-id="' + row.id + '">Delete</a>';
                        tableBody += '</td>';
                        tableBody += '</tr>';
                    }
                } else {
                    tableBody = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #6c757d;">📝 Tidak ada data artikel</td></tr>';
                }

                $('#artikelTable tbody').html(tableBody);
                showAlert(`✅ Semua data berhasil dimuat! Total: ${data.length} artikel`, 'success');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#artikelTable tbody').html(
                    '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #dc3545;">' +
                    '❌ Error loading all data: ' + error +
                    '</td></tr>'
                );
                showAlert('❌ Gagal memuat semua data: ' + error, 'danger');
            }
        });
    }

    // Make functions global
    window.loadData = loadData;
    window.loadAllData = loadAllData;

    // Load data on page ready
    loadData();
    
    // Handle delete button clicks
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var judul = $(this).closest('tr').find('td:nth-child(2)').text();
        
        if (confirm('⚠️ Apakah Anda yakin ingin menghapus artikel "' + judul + '"?\n\nTindakan ini tidak dapat dibatalkan!')) {
            // Show loading state
            $(this).html('⏳ Deleting...').prop('disabled', true);
            
            $.ajax({
                url: "<?= base_url('ajax/delete/') ?>" + id,
                method: "POST", // Changed from DELETE to POST as per your controller
                dataType: "json",
                success: function(response) {
                    console.log('Delete response:', response);
                    
                    if (response.status === 'OK') {
                        showAlert('✅ ' + response.message, 'success');
                        loadData(); // Reload data to reflect changes
                    } else {
                        showAlert('❌ ' + response.message, 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete Error:', error);
                    console.error('Response:', xhr.responseText);
                    showAlert('❌ Error deleting article: ' + error, 'danger');
                    
                    // Reset button state
                    $('.btn-delete[data-id="' + id + '"]').html('🗑️ Delete').prop('disabled', false);
                }
            });
        }
    });
    
    // Auto-refresh every 30 seconds (optional)
    // setInterval(loadData, 30000);
});
</script>

<?= $this->include('template/footer'); ?>
