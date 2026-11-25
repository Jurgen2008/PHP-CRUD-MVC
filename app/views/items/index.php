<div class="heading"><h1>Voorraadoverzicht</h1><a class="button primary" href="?action=create">+ Artikel toevoegen</a></div>
<section class="panel" aria-label="Artikelen">
<form class="toolbar" method="get" role="search">
<label for="q">Zoek op artikelnaam</label>
<div class="search"><input id="q" type="search" name="q" value="<?= h($search) ?>"><button>Zoeken</button>
<?php if ($search !== ''): ?><a href="?action=index">Wis zoekopdracht</a><?php endif; ?></div>
</form>
<?php if ($items): ?>
<table><caption><?= count($items) ?> <?= count($items) === 1 ? 'artikel' : 'artikelen' ?></caption>
<thead><tr><th scope="col">Artikel</th><th scope="col">Inhoud</th><th scope="col">Prijs</th><th scope="col">Voorraad</th><th scope="col">Acties</th></tr></thead>
<tbody><?php foreach ($items as $item): ?>
<tr><th scope="row"><?= h($item['naam']) ?></th><td data-label="Inhoud"><?= h($item['inhoud']) ?></td>
<td data-label="Prijs">€ <?= h(number_format((float)$item['prijs'], 2, ',', '.')) ?></td><td data-label="Voorraad" class="number"><?= h($item['voorraad']) ?></td>
<td class="actions"><a href="?action=edit&amp;id=<?= (int)$item['id'] ?>" aria-label="Bewerken: <?= h($item['naam']) ?>">Bewerken</a>
<a class="danger-link" href="?action=delete&amp;id=<?= (int)$item['id'] ?>" aria-label="Verwijderen: <?= h($item['naam']) ?>">Verwijderen</a></td></tr>
<?php endforeach; ?></tbody></table>
<?php else: ?><div class="empty"><h2><?= $search === '' ? 'De voorraad is leeg' : 'Geen artikelen gevonden' ?></h2>
<p><?= $search === '' ? 'Voeg je eerste artikel toe aan de voorraad.' : 'Probeer een andere artikelnaam.' ?></p></div><?php endif; ?>
</section>
