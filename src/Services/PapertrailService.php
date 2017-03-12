<?php

namespace Laravel\Forge\Services;

use Laravel\Forge\Contracts\ServiceContract;

class PapertrailService extends AbstractService implements ServiceContract
{
    /**
     * @{inheritdoc}
     */
    public function name()
    {
        return 'papertrail';
    }

    /**
     * @{inheritdoc}
     */
    public function installable()
    {
        return true;
    }

    /**
     * @{inheritdoc}
     */
    public function uninstallable()
    {
        return true;
    }
}