<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Raporlama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Raporlama</h1>
        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Aidat Özeti</h3>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Apartman ID</th>
                            <th>Toplam Tutar (TL)</th>
                            <th>Ay</th>
                        </tr>
                    </thead>
                    <tbody id="duesTableBody"></tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h3>Sorun Özeti</h3>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Açıklama</th>
                            <th>Apartman ID</th>
                            <th>Durum</th>
                            <th>Oluşturulma Tarihi</th>
                        </tr>
                    </thead>
                    <tbody id="issuesTableBody"></tbody>
                </table>
            </div>
        </div>
        <p id="result" class="mt-3 text-success fw-bold"></p>
    </div>

    <script>
        async function fetchDues() {
            const response = await fetch('http://localhost/site_management/api/dues.php');
            const data = await response.json();
            const tableBody = document.getElementById('duesTableBody');
            tableBody.innerHTML = ''; // Tabloyu temizle
            // Basit özet: Her apartman için toplam tutar
            const summary = {};
            data.forEach(dues => {
                if (!summary[dues.apartment_id]) {
                    summary[dues.apartment_id] = { total: 0, month: dues.month };
                }
                summary[dues.apartment_id].total += parseFloat(dues.amount || 0);
            });
            Object.keys(summary).forEach(aptId => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${aptId}</td>
                    <td>${summary[aptId].total.toFixed(2)}</td>
                    <td>${summary[aptId].month}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        async function fetchIssues() {
            const response = await fetch('http://localhost/site_management/api/issues.php');
            const data = await response.json();
            const tableBody = document.getElementById('issuesTableBody');
            tableBody.innerHTML = ''; // Tabloyu temizle
            data.forEach(issue => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${issue.description || 'Belirtilmemiş'}</td>
                    <td>${issue.apartment_id || 'Belirtilmemiş'}</td>
                    <td>${issue.status || 'Belirtilmemiş'}</td>
                    <td>${issue.created_at || 'Belirtilmemiş'}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // İlk yüklemede verileri çek
        fetchDues();
        fetchIssues();
    </script>
</body>
</html>