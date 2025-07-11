<?php
$path = __DIR__;
foreach (glob("$path/*.php") as $file) {
    if (basename($file) === 'autoload.php') continue;
    include $file;
}
?>
