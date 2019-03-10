<?php require_once './config.php'; ?>

<p id="migration" class='text-center'></p>

<script type='text/javascript'>
var migrationDate = new Date("<?=$config['core']['lastNestMigration']?>");
while (migrationDate < new Date()) {
  migrationDate = addDays(migrationDate, 14);
}

$("#migration").countdown(migrationDate, function(event) {
  console.log(migrationDate);
  var msg = "The next <b>nest migration</b> occurs in<br/> ";
  var time = event.strftime("%w %!w:<span>week</span>,<span>weeks</span>;, %d %!d:<span>day</span>,<span>days</span>;, %H %!H:<span>hour</span>,<span>hours</span>;, %M %!M:<span>minute</span>,<span>minutes</span>;, and %S %!S:<span>second</span>,<span>seconds</span>;");
  $(this).html(msg + time + "<br/>on<br/><span>" + moment(migrationDate).format("dddd MMMM Do YYYY, h:mm:ss A") + "</span>");
});
</script>