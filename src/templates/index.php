<style>
.mdl-data-table {
  margin: 16px auto;
}
</style>
<div id="history-chart" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
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
<p>Good job! Everything looks great! 😃</p>
<?php endif; ?>
<script>
$(document).ready(function () {
    $('#history-chart').highcharts({
        chart: {
            type: 'area'
        },
        title: {
            text: 'Quality Issues Over Time',
            x: -20 //center
        },
        subtitle: {
            text: 'The lower the better',
            x: -20
        },
        xAxis: {
            categories: <?= json_encode($executions) ?>,
            tickmarkPlacement: 'on',
            title: {
                enabled: false
            }
        },
        yAxis: {
            title: {
                text: 'Issues'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ' issues'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        plotOptions: {
            area: {
                stacking: 'normal',
                lineColor: '#666666',
                lineWidth: 1,
                marker: {
                    lineWidth: 1,
                    lineColor: '#666666'
                }
            }
        },
        series: <?= json_encode($historyData) ?>
    });
});
</script>