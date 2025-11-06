<?php
file_put_contents(__DIR__.'/test.txt', "ok ".date('c'));
echo is_writable(__DIR__) ? 'writable' : 'not writable';
?>
