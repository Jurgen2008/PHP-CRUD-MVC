<a class="back" href="?action=index">â€¹ Voorraadoverzicht</a>
<h1><?= h($title) ?></h1>
<form class="panel editor" method="post" novalidate>
<input type="hidden" name="token" value="<?= h($_SESSION['token']) ?>">
<?php if ($errors): ?><div class="errors" role="alert" tabindex="-1" autofocus><strong>Controleer de gemarkeerde velden.</strong><ul>
<?php foreach ($errors as $field => $error): ?><li><a href="#<?= h($field) ?>"><?= h(ucfirst($field)) ?></a></li><?php endforeach; ?>
</ul></div><?php endif; ?>
<?php foreach (['naam' => 'Artikelnaam', 'inhoud' => 'Inhoud', 'prijs' => 'Prijs (€)', 'voorraad' => 'Voorraad (stuks)'] as $field => $label): ?>
<div class="field"><label for="<?= $field ?>"><?= $label ?></label>
<?php $attrs = isset($errors[$field]) ? 'aria-invalid="true" aria-describedby="error-' . $field . '"' : ''; ?>
<input id="<?= $field ?>" name="<?= $field ?>" value="<?= h($values[$field]) ?>" required <?= $attrs ?>
<?= match ($field) {
    'voorraad' => 'type="number" min="0" max="2147483647" step="1"',
    'prijs' => 'inputmode="decimal"',
    default => 'maxlength="100"'
} ?>>
<?php if (isset($errors[$field])): ?><p class="error" id="error-<?= $field ?>"><?= h($errors[$field]) ?></p><?php endif; ?></div>
<?php endforeach; ?>
<div class="buttons"><button class="primary">Opslaan</button><a href="?action=index">Annuleren</a></div></form>
