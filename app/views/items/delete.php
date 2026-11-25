<a class="back" href="?action=index">‹ Voorraadoverzicht</a>
<h1>Artikel verwijderen</h1>
<form class="panel editor" method="post">
<p>Wil je <strong><?= h($item['naam']) ?></strong> uit de voorraad verwijderen? Je kunt dit niet ongedaan maken.</p>
<input type="hidden" name="token" value="<?= h($_SESSION['token']) ?>">
<div class="buttons"><button class="danger">Definitief verwijderen</button><a href="?action=index">Annuleren</a></div></form>
