<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Beanstalk\Action;

use Fusio\Engine\ActionAbstract;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use Pheanstalk\Pheanstalk;
use PSX\Http\Environment\HttpResponseInterface;

/**
 * Action which publishes a message to a queue
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class BeanstalkPublish extends ActionAbstract
{
    public function getName(): string
    {
        return 'Beanstalk-Publish';
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): HttpResponseInterface
    {
        $connection = $this->getConnection($configuration);

        $connection
            ->useTube($request->get('tube'))
            ->put($request->get('payload'));

        return $this->response->build(200, [], [
            'success' => true,
            'message' => 'Message successful published',
        ]);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The Beanstalk connection which should be used'));
    }

    protected function getConnection(ParametersInterface $configuration): Pheanstalk
    {
        $connection = $this->connector->getConnection($configuration->get('connection'));
        if (!$connection instanceof Pheanstalk) {
            throw new ConfigurationException('Given connection must be a Beanstalk connection');
        }

        return $connection;
    }
}
