<?php

use Plex\Command\CreateDatabase;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

return new FactoryCommandLoader([
    //    'app:heavy' => function () { return new HeavyCommand(); },
    'Create' => function () {return new CreateDatabase(); },
 ]);
