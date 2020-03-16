<?php
echo "<pre><hr><h1>Debugger</h1>\n";

echo "</blockquote>\n\n<b>INCLUDED FILES</b>\n<blockquote>";
print_r(get_included_files());

echo "</blockquote>\n\n<b>CONSTANTS</b>\n<blockquote>";
$constarray = get_defined_constants(true);
print_r($constarray['user']);

echo "</blockquote>\n\n<b>GLOBALS</b>\n<blockquote>";
print_r($GLOBALS);

echo "</blockquote>\n\n<b>OBJECTS - DUMPING " . '$this' . "</b>\n<blockquote>";
print_r($this);

echo "</blockquote>\n\n<b>ALL MODULES COMPILED INTO PHP INTERPRETER</b>\n<blockquote>";
print_r(get_loaded_extensions());

echo "\n<hr></pre>";