<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Beanstalk\Connection;

use Fusio\Engine\Connection\PingableInterface;
use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Contract\PheanstalkInterface;

/**
 * Beanstalk
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class Beanstalk implements ConnectionInterface, PingableInterface
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
