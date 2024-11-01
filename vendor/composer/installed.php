<?php return array(
    'root' => array(
        'name' => 'shutterpress/shutterpress-gallery',
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'reference' => 'c31ef964f8e486f7399866994370e99388766cf5',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shutterpress/shutterpress-gallery' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => 'c31ef964f8e486f7399866994370e99388766cf5',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'wpackagist-plugin/meta-box' => array(
            'pretty_version' => '5.10.2',
            'version' => '5.10.2.0',
            'reference' => 'tags/5.10.2',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../meta-box/meta-box',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
