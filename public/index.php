<?php
declare(strict_types=1);
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../app/models/Item.php';
require __DIR__ . '/../app/controllers/ItemController.php';

session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
$_SESSION['token'] ??= bin2hex(random_bytes(32));
header('Content-Type: text/html; charset=UTF-8');

function h(string|int $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function view(string $page, string $title, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../app/views/layout.php';
}

function problem(int $status, string $message): never
{
    http_response_code($status);
    view('message', 'Verzoek mislukt', compact('message'));
    exit;
}

function success(string $message): never
{
    $_SESSION['flash'] = $message;
    header('Location: ?action=index', true, 303);
    exit;
}

$action = $_GET['action'] ?? 'index';
if (!in_array($action, ['index', 'create', 'edit', 'delete'], true)) {
    problem(404, 'Deze pagina bestaat niet.');
}
$methods = $action === 'index' ? ['GET'] : ['GET', 'POST'];
if (!in_array($_SERVER['REQUEST_METHOD'], $methods, true)) {
    header('Allow: ' . implode(', ', $methods));
    problem(405, 'Open de pagina opnieuw via het overzicht.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!is_string($_POST['token'] ?? null) || !hash_equals($_SESSION['token'], $_POST['token']))) {
    problem(403, 'Je formulier is verlopen. Open het opnieuw vanuit het overzicht.');
}
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 4294967295]]);
if (in_array($action, ['edit', 'delete'], true) && !$id) {
    problem(404, 'Dit artikel bestaat niet.');
}
try {
    $controller = new ItemController(new Item(database()));
    match ($action) {
        'index' => $controller->index(),
        'create' => $controller->form(null),
        'edit' => $controller->form($id),
        'delete' => $controller->remove($id),
    };
} catch (PDOException $e) {
    error_log($e->getMessage());
    problem(503, 'De voorraad is tijdelijk niet beschikbaar. Controleer de databaseverbinding en probeer het opnieuw.');
}
