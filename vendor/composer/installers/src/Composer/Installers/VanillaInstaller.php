<?php
namespace Composer\Installers;

class VanillaInstaller extends BaseInstaller
{
    protected $locations = array(
        'Updater' => 'plugins/{$name}/',
        'theme'   => 'themes/{$name}/',
    );
}
