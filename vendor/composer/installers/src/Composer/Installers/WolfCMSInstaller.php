<?php
namespace Composer\Installers;

class WolfCMSInstaller extends BaseInstaller
{
    protected $locations = array(
        'Updater' => 'wolf/plugins/{$name}/',
    );
}
