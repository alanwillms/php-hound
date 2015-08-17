<style>
.breadcrumb,
.mdl-card {
  margin: 16px;
  min-height: 0;
  width: auto;
}

.mdl-card__title {
  background-color: rgb(103,58,183);
  color: #fff;
}

.mdl-card__supporting-text p:last-child {
  margin-bottom: 0;
}

.line-number {
  background-color: #fff;
  border: 1px solid #eee;
  border-bottom: 0;
  color: #999;
  display: inline-block;
  margin-right: 16px;
  padding: 0 16px;
}

.mdl-tooltip {
  max-width: 100%;
}

.has-issues,
.has-issues .line-number {
  background-color: #FFCDD2;
}
</style>
<div class="breadcrumb mdl-color-text--grey-500">
  <a href="index.html">Overview</a> &gt;
  <?= $fileName ?>
</div>
<div class="mdl-card mdl-shadow--2dp">
  <div class="mdl-card__supporting-text">
    <?php foreach ($lines as $lineNumber => $issues) : ?>
    <?php foreach ($issues as $issue) : ?>
    <p>
      <a href="#line<?= $lineNumber ?>"><strong><?= $lineNumber ?></strong>:
      <?= $issue['message'] ?></a>
    </p>
    <?php endforeach; ?>
    <?php endforeach; ?>
    <div class="code"><?= $fileContent ?></div>
  </div>
</div>