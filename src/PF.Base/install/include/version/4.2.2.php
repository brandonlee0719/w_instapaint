<?php

return function(Phpfox_Installer $Installer) {
	$Installer->db->update(':block',[
		'component' => 'category'
	], [
		'module_id' => 'marketplace',
		'm_connection' => 'marketplace.view'
	]);
};