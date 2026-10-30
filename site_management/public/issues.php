<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sorun Kaydı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Sorun Kaydı</h1>
        <form id="issuesForm" class="mt-4 p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label class="form-label">Açıklama</label>
                <input type="text" class="form-control" id="description">
            </div>
            <div class="mb-3">
                <label class="form-label">Apartman ID</label>
                <input type="number" class="form-control" id="apartment_id">
            </div>
            <div class="mb-3">
                <label class="form-label">Atanan Kişi</label>
                <input type="text" class="form-control" id="assigned_to">
            </div>
            <button type="submit" class="btn btn-primary w-100">Kaydet</button>
        </form>
        <p id="result" class="mt-3 text-success fw-bold"></p>
        <div id="issuesList" class="mt-4">
            <h3>Sorun Listesi</h3>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Açıklama</th>
                        <th>Apartman ID</th>
                        <th>Durum</th>
                        <th>Atanan Kişi</th>
                        <th>Oluşturulma Tarihi</th>
                    </tr>
                </thead>
                <tbody id="issuesTableBody"></tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('issuesForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                description: document.getElementById('description').value,
                apartment_id: document.getElementById('apartment_id').value,
                status: 'open',
                assigned_to: document.getElementById('assigned_to').value || null
            };
            try {
                const response = await fetch('http://localhost/site_management/api/issues.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                document.getElementById('result').innerText = result.message;
                if (result.status === 'success') fetchIssues();
            } catch (error) {
                document.getElementById('result').innerText = 'Hata: ' + error.message;
            }
        });

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
                    <td>${issue.assigned_to || 'Belirtilmemiş'}</td>
                    <td>${issue.created_at || 'Belirtilmemiş'}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // İlk yüklemede sorunları çek
        fetchIssues();
    </script>
</body>
</html>