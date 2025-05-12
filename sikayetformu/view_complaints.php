<?php
require_once('../config/db.php');

try {
    $stmt = $conn->query("SELECT * FROM complaints ORDER BY created_at DESC");
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şikayet Kayıtları</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .complaints-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .complaints-table th,
        .complaints-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .complaints-table th {
            background-color: #4d94ff;
            color: white;
        }

        .complaints-table tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .complaints-table tr:hover {
            background-color: #e0e0e0;
        }

        .urgency-kirmizi {
            color: #f44336;
            font-weight: bold;
        }

        .urgency-sari {
            color: #ff9800;
            font-weight: bold;
        }

        .urgency-yesil {
            color: #4caf50;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-hospital"></i> Triaj Sistemi</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="triage.html">Ana Sayfa</a></li>
                    <li><a href="#">Hakkımızda</a></li>
                    <li><a href="#">Triaj Nedir?</a></li>
                    <li><a href="#">İletişim</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="triage-container">
        <h2>Şikayet Kayıtları</h2>
        
        <table class="complaints-table">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Şikayetler</th>
                    <th>Açıklama</th>
                    <th>Aciliyet Seviyesi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td><?php echo date('d.m.Y H:i', strtotime($complaint['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($complaint['diseases']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['description']); ?></td>
                        <td class="urgency-<?php echo $complaint['urgency_level']; ?>">
                            <?php 
                                switch($complaint['urgency_level']) {
                                    case 'kirmizi':
                                        echo 'Kırmızı Alan';
                                        break;
                                    case 'sari':
                                        echo 'Sarı Alan';
                                        break;
                                    case 'yesil':
                                        echo 'Yeşil Alan';
                                        break;
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>