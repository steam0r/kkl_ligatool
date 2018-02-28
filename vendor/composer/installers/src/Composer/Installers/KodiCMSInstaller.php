<?php
namespace Composer\Installers;

class KodiCMSInstaller extends BaseInstaller
{
    protected $locations = array(
        'Updater' => 'cms/plugins/{$name}/',
        'media'   => 'cms/media/vendor/{$name}/'
    );
}
