<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2016 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Beanstalk\Tests\Action;

use Fusio\Adapter\Beanstalk\Action\BeanstalkPush;
use Fusio\Adapter\Beanstalk\Tests\BeanstalkTestCase;
use Fusio\Engine\Form\Builder;
use Fusio\Engine\Form\Container;
use Fusio\Engine\Response;
use Fusio\Engine\ResponseInterface;
use Pheanstalk\Job;
use PSX\Record\Record;

/**
 * BeanstalkPushTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class BeanstalkPushTest extends BeanstalkTestCase
{
    public function testHandle()
    {
        $parameters = $this->getParameters([
            'connection' => 1,
            'queue'      => 'foo_queue',
        ]);

        $data     = Record::fromArray(['foo' => 'bar']);
        $action   = $this->getActionFactory()->factory(BeanstalkPush::class);
        $response = $action->handle($this->getRequest('POST', [], [], [], $data), $parameters, $this->getContext());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([], $response->getHeaders());
        $this->assertEquals(['success' => true, 'message' => 'Push was successful'], $response->getBody());

        // check whether we can get the message from the queue
        $job = $this->connection->reserveFromTube('foo_queue');

        $this->assertInstanceOf(Job::class, $job);
        $this->assertJsonStringEqualsJsonString('{"foo": "bar"}', $job->getData());

        $this->connection->delete($job);
    }

    public function testGetForm()
    {
        $action  = $this->getActionFactory()->factory(BeanstalkPush::class);
        $builder = new Builder();
        $factory = $this->getFormElementFactory();

        $action->configure($builder, $factory);

        $this->assertInstanceOf(Container::class, $builder->getForm());
    }
}
