<?php
namespace Composer\Installers;

class ReIndexInstaller extends BaseInstaller
{
    protected $locations = array(
        'theme'   => 'themes/{$name}/',
        'Updater' => 'plugins/{$name}/'
    );
}
