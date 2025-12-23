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

namespace Fusio\Adapter\Beanstalk\Tests;

use Fusio\Adapter\Beanstalk\Adapter;
use Fusio\Adapter\Beanstalk\Connection\Beanstalk;
use Fusio\Engine\Model\Connection;
use Fusio\Engine\Parameters;
use Fusio\Engine\Repository\ConnectionMemory;
use Fusio\Engine\Test\CallbackConnection;
use Fusio\Engine\Test\EngineTestCaseTrait;
use Pheanstalk\Pheanstalk;
use PHPUnit\Framework\TestCase;

/**
 * BeanstalkTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
abstract class BeanstalkTestCase extends TestCase
{
    use EngineTestCaseTrait;

    protected ?Pheanstalk $connection = null;

    protected function setUp(): void
    {
        if (!$this->connection) {
            $this->connection = $this->newConnection();
        }

        $connection = new Connection(1, 'foo', CallbackConnection::class, [
            'callback' => function(){
                return $this->connection;
            },
        ]);

        /** @var ConnectionMemory $repository */
        $repository = $this->getConnectionRepository();
        $repository->add($connection);
    }

    protected function newConnection(): Pheanstalk
    {
        $connector = new Beanstalk();

        try {
            $connection = $connector->getConnection(new Parameters([
                'host' => '127.0.0.1',
                'port' => '11300',
            ]));

            return $connection;
        } catch (\Exception $e) {
            $this->markTestSkipped('Beanstalkd connection not available');
        }
    }

    protected function getAdapterClass(): string
    {
        return Adapter::class;
    }
}
