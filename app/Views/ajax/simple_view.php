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

    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 40px 0;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .page-header h1 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 300;
        color: #2c3e50;
        letter-spacing: -0.5px;
    }

    /* Simple Table Design */
    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .table-data th,
        .table-data td {
            padding: 12px 8px;
            font-size: 13px;
        }

        .btn {
            padding: 8px 12px;
            margin: 2px 0;
            display: block;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .table-data th,
        .table-data td {
            padding: 10px 6px;
            font-size: 12px;
        }

        .page-header {
            padding: 30px 15px;
        }
    }
</style>

<div class="container">
    <div class="page-header">
        <h1>Data Artikel</h1>
    </div>

    <div class="table-container">
        <table class="table-data" id="artikelTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" class="loading">Loading data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>
<script>
$(document).ready(function() {
    // Function to display a loading message while data is fetched
    function showLoadingMessage() {
        $('#artikelTable tbody').html('<tr><td colspan="4" class="loading">Loading data...</td></tr>');
    }

    // Buat fungsi load data
    function loadData() {
        showLoadingMessage(); // Display loading message initially
        
        // Lakukan request AJAX ke URL getData
        $.ajax({
            url: "<?= base_url('ajax/getData') ?>",
            method: "GET",
            dataType: "json",
            success: function(data) {
                // Tampilkan data yang diterima dari server
                var tableBody = "";

                if (data && data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        var statusClass = row.status == 1 ? 'status-published' : 'status-draft';
                        var statusText = row.status == 1 ? 'Published' : 'Draft';

                        tableBody += '<tr>';
                        tableBody += '<td><span class="id-number">' + row.id + '</span></td>';
                        tableBody += '<td>' + (row.judul || 'No Title') + '</td>';
                        tableBody += '<td><span class="status ' + statusClass + '">' + statusText + '</span></td>';
                        tableBody += '<td>';
                        tableBody += '<a href="<?= base_url('artikel/') ?>' + (row.slug || row.id) + '" class="btn btn-primary">View</a>';
                        tableBody += '<a href="<?= base_url('admin/artikel/edit/') ?>' + row.id + '" class="btn btn-primary">Edit</a>';
                        tableBody += '<a href="#" class="btn btn-danger btn-delete" data-id="' + row.id + '">Delete</a>';
                        tableBody += '</td>';
                        tableBody += '</tr>';
                    }
                } else {
                    tableBody = '<tr><td colspan="4" style="text-align: center; padding: 40px; color: #6c757d;">📝 Tidak ada data artikel</td></tr>';
                }

                $('#artikelTable tbody').html(tableBody);
            },
            error: function(xhr, status, error) {
                $('#artikelTable tbody').html(
                    '<tr><td colspan="4" style="text-align: center; padding: 40px; color: #dc3545; background: #f8d7da;">' +
                    '❌ Error loading data: ' + error +
                    '</td></tr>'
                );
            }
        });
    }

    loadData();

    // Implement actions for buttons (e.g., delete confirmation)
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        // hapus data;
        if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
            $.ajax({
                url: "<?= base_url('ajax/delete/') ?>" + id,
                method: "POST", // Changed from DELETE to POST
                dataType: "json",
                success: function(response) {
                    if (response.status === 'OK') {
                        alert('Artikel berhasil dihapus!');
                        loadData(); // Reload data to reflect changes
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error deleting article: ' + textStatus + ' ' + errorThrown);
                }
            });
        }
        console.log('Delete button clicked for ID:', id);
    });
});
</script>

<?= $this->include('template/footer'); ?>
