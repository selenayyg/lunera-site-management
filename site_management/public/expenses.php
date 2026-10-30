<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Gider Kaydı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Gider Kaydı</h1>
        <form id="expensesForm" class="mt-4 p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label class="form-label">Açıklama</label>
                <input type="text" class="form-control" id="description" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tutar (TL)</label>
                <input type="number" class="form-control" id="amount" step="0.01" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tarih</label>
                <input type="date" class="form-control" id="expense_date" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Kaydet</button>
        </form>
        <p id="result" class="mt-3 text-success fw-bold"></p>
        <canvas id="expensesChart" class="mt-5 w-100" style="max-height: 300px;"></canvas>
    </div>

    <script>
        let expensesChart = null; // Chart nesnesini saklamak için global değişken

        document.getElementById('expensesForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                description: document.getElementById('description').value,
                amount: document.getElementById('amount').value,
                expense_date: document.getElementById('expense_date').value,
                user_id: null // Şimdilik boş bırak
            };
            try {
                const response = await fetch('http://localhost/site_management/api/expenses.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                document.getElementById('result').innerText = result.message;
                if (result.status === 'success') fetchChartData();
            } catch (error) {
                document.getElementById('result').innerText = 'Hata: ' + error.message;
            }
        });

        async function fetchChartData() {
            const response = await fetch('http://localhost/site_management/api/expenses.php');
            const data = await response.json();
            // Mevcut Chart varsa yok et
            if (expensesChart) {
                expensesChart.destroy();
            }
            // description bazında verileri grupla
            const groupedData = {};
            data.forEach(item => {
                if (!groupedData[item.description]) {
                    groupedData[item.description] = { amounts: [], dates: [] };
                }
                groupedData[item.description].amounts.push(item.amount);
                groupedData[item.description].dates.push(item.expense_date);
            });

            const labels = Object.keys(groupedData).map(desc => desc); // description'lar etiketler
            const datasets = Object.keys(groupedData).map((desc, index) => ({
                label: desc, // Her barın adı
                data: groupedData[desc].amounts,
                backgroundColor: getColor(index), // Farklı renkler
                borderColor: '#fff',
                borderWidth: 1
            }));

            // Yeni Chart oluştur
            expensesChart = new Chart(document.getElementById('expensesChart'), {
                type: 'bar',
                data: {
                    labels: data.map(d => d.expense_date || 'Bilinmeyen'), // x ekseni tarihler
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Tutar (TL)' } } },
                    plugins: {
                        legend: { position: 'top' }, // Description etiketleri üstte
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} TL`; // Tooltip'ta açıklama ve tutar
                                }
                            }
                        }
                    }
                }
            });
        }

        // Renkleri dinamik olarak üret
        function getColor(index) {
            const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
            return colors[index % colors.length];
        }

        fetchChartData(); // İlk yüklemede grafik çek
    </script>
</body>
</html>