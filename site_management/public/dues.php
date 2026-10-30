<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Aidat Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Aidat Yönetimi</h1>
        <form id="duesForm" class="p-4 border rounded shadow-sm bg-light">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Apartman ID</label>
                    <input type="number" class="form-control" id="duesAptId" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tutar (TL)</label>
                    <input type="number" class="form-control" id="duesAmount" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ay</label>
                    <input type="month" class="form-control" id="duesMonth" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4">Kaydet</button>
                </div>
            </div>
        </form>
        <p id="duesResult" class="mt-3 text-success fw-bold"></p>

        <h3 class="mt-4">Aidat Listesi</h3>
        <table class="table table-striped mt-3" id="duesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Apartman ID</th>
                    <th>Tutar</th>
                    <th>Ay</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody id="duesTableBody"></tbody>
        </table>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            async function loadDues() {
                const response = await fetch('http://localhost/site_management/api/dues.php');
                const dues = await response.json();
                const tbody = document.getElementById('duesTableBody');
                tbody.innerHTML = '';
                dues.forEach(dues => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${dues.id}</td>
                        <td>${dues.apartment_id}</td>
                        <td>${dues.amount} TL</td>
                        <td>${dues.month}</td>
                        <td>${dues.paid ? 'Ödendi' : 'Ödenmedi'}</td>
                        <td><button class="btn btn-success btn-sm pay-btn" data-id="${dues.id}">Öde</button></td>
                    `;
                    tbody.appendChild(row);
                });
            }

            document.getElementById('duesForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const data = {
                    action: 'create',
                    apartment_id: document.getElementById('duesAptId').value,
                    amount: document.getElementById('duesAmount').value,
                    month: document.getElementById('duesMonth').value
                };
                try {
                    const response = await fetch('http://localhost/site_management/api/dues.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    document.getElementById('duesResult').innerText = result.message;
                    if (result.status === 'success') loadDues();
                } catch (error) {
                    document.getElementById('duesResult').innerText = 'Hata: ' + error.message;
                }
            });

            // Ödeme simülasyonu
            document.addEventListener('click', async (e) => {
                if (e.target.classList.contains('pay-btn')) {
                    const id = e.target.getAttribute('data-id');
                    const data = { action: 'pay', id: id };
                    try {
                        const response = await fetch('http://localhost/site_management/api/dues.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify(data)
                        });
                        const result = await response.json();
                        alert(result.message);
                        if (result.status === 'success') loadDues();
                    } catch (error) {
                        alert('Hata: ' + error.message);
                    }
                }
            });

            // Sayfa yüklendiğinde listeyi yükle
            loadDues();
        </script>
    </div>
</body>
</html>