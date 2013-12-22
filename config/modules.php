<?php

/**
 * Register application modules
 */
$application->registerModules(array(
	'frontend' => array(
		'className' => 'Robinson\Frontend\Module',
		'path' => __DIR__ . '/../apps/frontend/Module.php'
	),
        'backend' => array
        (
            'className' => 'Robinson\Backend\Module',
            'path' => __DIR__ . '/../apps/backend/Module.php',
        )
));