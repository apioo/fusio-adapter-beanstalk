<?php
/*
 * Fusio - Self-Hosted API Management for Builders.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Pheanstalk\Exception\ClientException;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Values\TubeList;

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
        $host = $config->get('host');
        if (empty($host)) {
            throw new ConfigurationException('No host configured');
        }

        $port = (int) $config->get('port');
        if ($port > 0) {
            return Pheanstalk::create($host, $port);
        } else {
            return Pheanstalk::create($host);
        }
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newInput('host', 'Host', 'text', 'The IP or hostname of the Beanstalk server'));
        $builder->add($elementFactory->newInput('port', 'Port', 'number', 'Optional the port of the Beanstalk server'));
    }

    public function ping(mixed $connection): bool
    {
        if (!$connection instanceof Pheanstalk) {
            return false;
        }

        return $connection->stats()->version !== '';
    }
}
