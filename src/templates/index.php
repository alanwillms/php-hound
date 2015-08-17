<style>
.mdl-data-table {
  margin: 16px auto;
}
</style>
<?php if (count($files) > 0) : ?>
<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
  <thead>
    <tr>
      <th class="mdl-data-table__cell--non-numeric">File</th>
      <th>Issues</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($files as $fileName => $issuesCount) : ?>
    <tr>
      <td class="mdl-data-table__cell--non-numeric"><a href="<?=
        str_replace(DIRECTORY_SEPARATOR, '_', $fileName) . '.html'
        ?>"><?= $fileName ?></a></td>
      <td><?= $issuesCount ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
<p>Good job! Everything seems fine! 😃</p>
<?php endif; ?>