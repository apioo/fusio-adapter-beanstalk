<?php
/*
 * Fusio is an open source API management platform which helps to create innovative API solutions.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Fusio\Adapter\Beanstalk\Connection;

use Fusio\Engine\Connection\PingableInterface;
use Fusio\Engine\ConnectionAbstract;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;

/**
 * Beanstalk
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class Beanstalk extends ConnectionAbstract implements PingableInterface
{
    public function getName(): string
    {
        return 'Beanstalk';
    }

    public function getConnection(ParametersInterface $config): Pheanstalk
    {
        $port = (int) $config->get('port');
        if (empty($port)) {
            $port = PheanstalkInterface::DEFAULT_PORT;
        }

        return Pheanstalk::create($config->get('host'), $port);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newInput('host', 'Host', 'text', 'The IP or hostname of the Beanstalk server'));
        $builder->add($elementFactory->newInput('port', 'Port', 'number', 'Optional the port of the Beanstalk server'));
    }

    public function ping(mixed $connection): bool
    {
        if ($connection instanceof Pheanstalk) {
            $stats = $connection->stats();
            return isset($stats['pid']) && $stats['pid'] > 0;
        } else {
            return false;
        }
    }
}
