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

namespace Fusio\Adapter\Beanstalk\Action;

use Fusio\Engine\ActionAbstract;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Values\TubeName;
use PSX\Http\Environment\HttpResponseInterface;
use PSX\Json\Parser;

/**
 * Action which publishes a message to a queue
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

        $tube = $configuration->get('tube');
        if (empty($tube)) {
            $tube = $request->get('tube');
            $payload = $request->get('payload');
        } else {
            $payload = Parser::encode($request->getPayload());
        }

        if (empty($tube)) {
            throw new ConfigurationException('No tube configured');
        }

        $connection->useTube(new TubeName($tube));
        $connection->put($payload);

        return $this->response->build(200, [], [
            'success' => true,
            'message' => 'Message successful published',
        ]);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The Beanstalk connection which should be used'));
        $builder->add($elementFactory->newInput('tube', 'Tube', 'text', 'The tube'));
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
