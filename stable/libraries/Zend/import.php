<?php
    $clientLibraryPath = dirname(__FILE__);
    $oldPath = set_include_path(get_include_path().PATH_SEPARATOR.$clientLibraryPath);
    require_once dirname(__FILE__).'/Zend/Loader.php';
?>