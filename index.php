<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Portalı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="/project/haber/favicon.png" rel="icon">
    <link href="/project/haber/favicon.png" rel="apple-touch-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4 text-center">Genel Haberler</h2>
        <div class="row">
            <?php
            // API isteği
            $url = "https://newsdata.io/api/1/news?apikey=" . API_KEY . "&language=tr&country=tr&size=10";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'User-Agent: MyNewsApp/1.0'
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200 && $response) {
                $data = json_decode($response, true);
            } else {
                $data = ['results' => []]; // Hata durumunda boş veri döndür
            }

            // Haberleri göster
            if (empty($data['results'])) {
                echo '<div class="alert alert-warning">Şu an için haber bulunamadı.</div>';
            } else {
                foreach ($data['results'] as $news) {
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($news['image_url'])) : ?>
                                <img src="<?php echo htmlspecialchars($news['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($news['title']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                                <p class="card-text">
                                    <?php echo isset($news['description']) ? substr(htmlspecialchars($news['description']), 0, 150) . '...' : 'Açıklama bulunamadı.'; ?>
                                </p>
                                <p class="text-muted">
                                    <small>Kaynak: <?php echo htmlspecialchars($news['source_id']); ?></small><br>
                                    <small>Tarih: <?php echo date('d.m.Y H:i', strtotime($news['pubDate'])); ?></small>
                                </p>
                                <a href="<?php echo htmlspecialchars($news['link']); ?>" class="btn btn-primary" target="_blank">Habere Git</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
