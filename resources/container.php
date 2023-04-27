<?php

use Fusio\Adapter\Beanstalk\Action\BeanstalkPublish;
use Fusio\Adapter\Beanstalk\Connection\Beanstalk;
use Fusio\Engine\Adapter\ServiceBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = ServiceBuilder::build($container);
    $services->set(Beanstalk::class);
    $services->set(BeanstalkPublish::class);
};
