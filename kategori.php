<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori - Haber Portalı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="/project/haber/favicon.png" rel="icon">
    <link href="/project/haber/favicon.png" rel="apple-touch-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <?php
        require_once 'config.php';

        // GET parametresi ile kategori seçme
        $category = isset($_GET['cat']) ? $_GET['cat'] : 'top';
        $categories = [
            'top' => 'Genel',
            'business' => 'Ekonomi',
            'technology' => 'Teknoloji',
            'sports' => 'Spor',
            'entertainment' => 'Magazin',
            'science' => 'Bilim',
            'health' => 'Sağlık'
        ];

        // Kategori başlığını belirle
        $categoryTitle = isset($categories[$category]) ? $categories[$category] : 'Genel';
        ?>

        <h2 class="mb-4"><?php echo $categoryTitle; ?> Haberleri</h2>

        <div class="row">
            <?php
            $url = "https://newsdata.io/api/1/news";
            $params = [
                'apikey' => API_KEY,
                'language' => 'tr',
                'country' => 'tr',
                'category' => $category,
                'size' => 10
            ];

            $fullUrl = $url . '?' . http_build_query($params);

            // cURL isteği başlat
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fullUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                echo '<div class="alert alert-danger">API bağlantı hatası! (HTTP Kodu: ' . $httpCode . ')</div>';
            } else {
                $data = json_decode($response, true);

                if (!empty($data['results'])) {
                    foreach ($data['results'] as $news) {
                        ?>
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($news['image_url'])) : ?>
                                <img src="<?php echo $news['image_url']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($news['title']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                                    <p class="card-text"><?php echo substr($news['description'] ?? '', 0, 150) . '...'; ?></p>
                                    <p class="text-muted">
                                        <small>Kaynak: <?php echo htmlspecialchars($news['source_id'] ?? 'Bilinmiyor'); ?></small><br>
                                        <small>Tarih: <?php echo date('d.m.Y H:i', strtotime($news['pubDate'])); ?></small>
                                    </p>
                                    <a href="<?php echo $news['link']; ?>" class="btn btn-primary" target="_blank">Habere Git</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="alert alert-warning">Şu an için uygun haber bulunamadı.</div>';
                }
            }
            ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
