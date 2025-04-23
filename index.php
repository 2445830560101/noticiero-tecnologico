<?php
require_once 'config.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyNewsApp'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$news = fetchData(NEWS_API_URL . $page . '&apiKey=' . NEWS_API_KEY);
$authors = fetchData(RANDOM_USER_API);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticiero Tecnológico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-5">Noticiero Tecnológico</h1>
        
        <?php if (isset($news['articles']) && count($news['articles']) > 0): ?>
            <div class="row">
                <?php foreach ($news['articles'] as $index => $article): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <?php if ($article['urlToImage']): ?>
                                <img src="<?= htmlspecialchars($article['urlToImage']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($article['description'], 0, 100)) ?>...</p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <?php if (isset($authors['results'][$index])): ?>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $authors['results'][$index]['picture']['thumbnail'] ?>" class="rounded-circle me-2" alt="Autor">
                                        <small class="text-muted">
                                            Por <?= $authors['results'][$index]['name']['first'] ?> <?= $authors['results'][$index]['name']['last'] ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted d-block mt-2">
                                    <?= date('d/m/Y', strtotime($article['publishedAt'])) ?> - 
                                    <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank">Leer más</a>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
         
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < 5): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php else: ?>
            <div class="alert alert-warning">No se encontraron noticias.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>