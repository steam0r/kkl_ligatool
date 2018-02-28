<?php
namespace Composer\Installers;

class EliasisInstaller extends BaseInstaller
{
    protected $locations = array(
        'component' => 'components/{$name}/',
        'module'    => 'modules/{$name}/',
        'Updater'   => 'plugins/{$name}/',
        'template'  => 'templates/{$name}/',
    );
}
