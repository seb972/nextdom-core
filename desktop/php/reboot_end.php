<?php
require_once dirname(__FILE__) . '/../../core/php/core.inc.php';
if (!isConnect('admin')) {
	throw new Exception(__('401 - Accès non autorisé', __FILE__));
}
if ($_GET['shut'] == 1) {
	nextdom::haltSystem();
} else {
	nextdom::rebootSystem();
}
?>