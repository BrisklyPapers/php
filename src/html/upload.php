<pre>
<?php

print_r($_REQUEST);
print_r($_FILES);

foreach($_FILES['files']['tmp_name'] as $tmpName) {
    if (!file_exists($tmpName)) {
        continue;
    }
//    echo file_get_contents($tmpName);
}