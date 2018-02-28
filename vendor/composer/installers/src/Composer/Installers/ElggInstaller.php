<?php
namespace Composer\Installers;

class ElggInstaller extends BaseInstaller
{
    protected $locations = array(
        'Updater' => 'mod/{$name}/',
    );
}
