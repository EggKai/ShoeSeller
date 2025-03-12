<?php
class Alert {
    private const Values = [
        0 => ['key' => 'None', 'color' => null],
        1 => ['key' => 'Success', 'color' => 'green'],
        2 => ['key' => 'Failure', 'color' => 'red'],
        3 => ['key' => 'FatalError', 'color' => 'red']
    ];
    public static function color($number): string {
        return self::Values[$number]['color'] ?? '';
    }
    public static function key($number): string {
        return self::Values[$number]['key'] ?? '';
    }
}
//echo Alert::color(1);  -> Output: 'green'
//echo Alert::key(2);    -> Output: 'Failure'
?>
<?php $alertLevel=$alert[1]??0?>
<?php if ($alertLevel != 0) { ?>
    <div class="<?php echo Alert::key($alertLevel); ?>">
        <span class="closebtn" onclick="closeAlert(this)">&times;</span> 
        <strong><?php echo Alert::key($alertLevel); ?></strong>: <?php echo $alert[0]; ?>
    </div>
<?php } ?>

<script>
function closeAlert(element) {
    element.parentElement.style.display = 'none';
}
</script>