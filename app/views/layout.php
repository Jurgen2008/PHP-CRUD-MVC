<!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title) ?> · Voorraadbeheer</title>
<link rel="stylesheet" href="style.css">
</head>
<body><a class="skip" href="#inhoud">Naar inhoud</a>
<header><div class="wrap brand"><span class="mark" aria-hidden="true">▦</span><a href="?action=index">Voorraadbeheer</a></div></header>
<main class="wrap" id="inhoud">
<?php if (isset($_SESSION['flash'])): ?>
<p class="notice" role="status"><?= h($_SESSION['flash']) ?></p>
<?php unset($_SESSION['flash']); endif; ?>
<?php require __DIR__ . '/items/' . $page . '.php'; ?>
</main></body></html>
