<?php
require 'vendor/autoload.php';

// Laravel version 5.1 needs at least support for php version 5.5.9
return Madewithlove\PhpCsFixer\Config::fromFolders(['src'], '5.5.9');