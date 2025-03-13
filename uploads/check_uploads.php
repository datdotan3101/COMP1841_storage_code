<?php
$dir = "uploads/";
if (!is_dir($dir)) {
    echo "File $dir does not exist!";
} else {
    echo "List of files in $dir:<br>";
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file !== "." && $file !== "..") {
            echo "<a href='$dir/$file' target='_blank'>$file</a><br>";
        }
    }
}
?>
