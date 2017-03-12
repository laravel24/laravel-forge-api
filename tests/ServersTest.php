<?php

namespace Laravel\Tests\Forge;

use Mockery;
use Laravel\Forge\Server;
use Laravel\Forge\ForgeServers;
use PHPUnit\Framework\TestCase;
use Laravel\Tests\Forge\Helpers\Api;
use Laravel\Tests\Forge\Helpers\FakeResponse;
use Laravel\Forge\Exceptions\Servers\ServerWasNotFoundException;

class ServersTests extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @dataProvider serversListDataProvider
     */
    public function testListServers(array $serversList)
    {
        // Servers List can be retrieved via API.

        // Create API provider.
        // Create Servers manager.
        // Fetch servers list.

        // Assert that servers list contains expected servers.

        $api = Api::fake(function ($http) use ($serversList) {
            $http->shouldReceive('request')
                ->with('GET', '/api/v1/servers')
                ->andReturn(
                    FakeResponse::fake()->withJson($serversList)->toResponse()
                );
        });

        $servers = new ForgeServers($api);
        $jsonServers = $serversList['servers'];

        foreach ($jsonServers as $jsonServer) {
            $server = $servers[$jsonServer['name']];

            $this->assertInstanceOf(Server::class, $server);
            $this->assertSame($jsonServer['name'], $server->name());
        }
    }

    /**
     * @dataProvider getServerDataProvider
     */
    public function testGetServer(array $data)
    {
        // Single server can be retrieved via API.

        // Create API provider.
        // Create Servers manager.
        // Load single server by ID.

        // Assert that server was loaded.

        $api = Api::fake(function ($http) use ($data) {
            $http->shouldReceive('request')
                ->with('GET', '/api/v1/servers/'.$data['id'])
                ->andReturn(
                    FakeResponse::fake()->withJson(['server' => $data])->toResponse()
                );
        });

        $servers = new ForgeServers($api);
        $server = $servers->get($data['id']);

        $this->assertInstanceOf(Server::class, $server);
        $this->assertSame($data['name'], $server->name());
    }

    /**
     * @dataProvider getServerFailDataProvider
     */
    public function testGetServerFail(int $serverId)
    {
        // If single server was not found, exception should be thrown.

        // Create API provider.
        // Create Servers manager.
        // Load single non-existed server by ID.

        // Assert that ServerWasNotFoundException was thrown.

        $api = Api::fake(function ($http) use ($serverId) {
            $http->shouldReceive('request')
                ->with('GET', '/api/v1/servers/'.$serverId)
                ->andReturn(FakeResponse::fake()->withStatus(404)->toResponse());
        });

        $servers = new ForgeServers($api);

        $this->expectException(ServerWasNotFoundException::class);
        $server = $servers->get($serverId);
    }

    /**
     * @dataProvider updateServerDataProvider
     */
    public function testUpdateServer(array $data, array $payload, array $response)
    {
        // Server data can be updated via API.

        // Create API provider.
        // Create Servers manager.
        // Load single server by ID.
        // Update server data.

        // Assert that server was updated.

        $api = Api::fake(function ($http) use ($data, $payload, $response) {
            $http->shouldReceive('request')
                ->with('GET', '/api/v1/servers/'.$data['id'])
                ->andReturn(
                    FakeResponse::fake()->withJson(['server' => $data])->toResponse()
                );

            $http->shouldReceive('request')
                ->with('PUT', '/api/v1/servers/'.$data['id'], ['form_params' => $payload])
                ->andReturn(
                    FakeResponse::fake()->withJson(['server' => $response])->toResponse()
                );
        });

        $servers = new ForgeServers($api);
        $server = $servers->get($data['id']);

        $this->assertTrue($server->update($payload));

        foreach ($payload as $field => $value) {
            $this->assertSame($value, $server[$field]);
        }
    }

    /**
     * @dataProvider deleteServerDataProvider
     */
    public function testDeleteServer(array $data)
    {
        // Server can be deleted via API.

        // Create API provider.
        // Create Servers manager.
        // Load single server by ID.
        // Delete server.

        // Assert that server was deleted.

        $api = Api::fake(function ($http) use ($data) {
            $http->shouldReceive('request')
                ->with('GET', '/api/v1/servers/'.$data['id'])
                ->andReturn(
                    FakeResponse::fake()->withJson(['server' => $data])->toResponse()
                );

            $http->shouldReceive('request')
                ->with('DELETE', '/api/v1/servers/'.$data['id'])
                ->andReturn(
                    FakeResponse::fake()->toResponse()
                );
        });

        $servers = new ForgeServers($api);
        $server = $servers->get($data['id']);

        $this->assertTrue($server->delete());
    }

    /**
     * @dataProvider serverOperationsDataProvider
     */
    public function testServerOperations(string $method, array $data, array $operation, array $response, $expectedResult)
    {
        $api = Api::fake(function ($http) use ($data, $operation, $response) {
            $http->shouldReceive('request')
                ->with('GET', '/api/v1/servers/'.$data['id'])
                ->andReturn(
                    FakeResponse::fake()->withJson(['server' => $data])->toResponse()
                );

            $http->shouldReceive('request')
                ->with($operation['method'], $operation['url'])
                ->andReturn(
                    FakeResponse::fake()->withJson($response)->toResponse()
                );
        });

        $servers = new ForgeServers($api);
        $server = $servers->get($data['id']);

        $this->assertSame($expectedResult, $server->{$method}());
    }

    public function serverData(array $replace = [])
    {
        return array_merge([
            'id' => 1,
            'name' => 'northrend',
            'size' => '512MB',
            'region' => 'Amsterdam 2',
            'php_version' => 'php71',
            'ip_address' => '37.139.3.148',
            'private_ip_address' => '10.129.3.252',
            'blackfire_status' => null,
            'papertail_status' => null,
            'revoked' => false,
            'created_at' => '2016-12-15 18:38:18',
            'is_ready' => true,
            'network' => [],
        ], $replace);
    }

    public function serversListDataProvider(): array
    {
        return [
            [
                'json' => [
                    'servers' => [
                        [
                            'id' => 1,
                            'name' => 'northrend',
                            'size' => '512MB',
                            'region' => 'Amsterdam 2',
                            'php_version' => 'php71',
                            'ip_address' => '37.139.3.148',
                            'private_ip_address' => '10.129.3.252',
                            'blackfire_status' => null,
                            'papertail_status' => null,
                            'revoked' => false,
                            'created_at' => '2016-12-15 18:38:18',
                            'is_ready' => true,
                            'network' => [],
                        ],
                        [
                            'id' => 2,
                            'name' => 'azeroth',
                            'size' => '512MB',
                            'region' => 'Amsterdam 2',
                            'php_version' => 'php71',
                            'ip_address' => '37.139.3.149',
                            'private_ip_address' => '10.129.3.253',
                            'blackfire_status' => null,
                            'papertail_status' => null,
                            'revoked' => false,
                            'created_at' => '2016-12-15 18:38:19',
                            'is_ready' => true,
                            'network' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getServerDataProvider(): array
    {
        return [
            [
                'data' => $this->serverData(),
            ],
        ];
    }

    public function getServerFailDataProvider(): array
    {
        return [
            [
                'serverId' => 1,
            ],
        ];
    }

    public function updateServerDataProvider(): array
    {
        return [
            [
                'data' => $this->serverData(),
                'payload' => [
                    'name' => 'azeroth',
                    'network' => [1, 2],
                    'private_ip_address' => '10.10.10.10',
                ],
                'response' => $this->serverData([
                    'name' => 'azeroth',
                    'network' => [1, 2],
                    'private_ip_address' => '10.10.10.10',
                ]),
            ],
        ];
    }

    public function deleteServerDataProvider(): array
    {
        return [
            [
                'data' => $this->serverData(),
            ],
        ];
    }

    public function serverOperationsDataProvider(): array
    {
        return [
            [
                'method' => 'reboot',
                'data' => $this->serverData(),
                'operation' => [
                    'method' => 'POST',
                    'url' => '/api/v1/servers/1/reboot',
                ],
                'response' => [],
                'expectedResult' => true,
            ],
            [
                'method' => 'revokeAccess',
                'data' => $this->serverData(),
                'operation' => [
                    'method' => 'POST',
                    'url' => '/api/v1/servers/1/revoke',
                ],
                'response' => [],
                'expectedResult' => true,
            ],
            [
                'method' => 'reconnect',
                'data' => $this->serverData(),
                'operation' => [
                    'method' => 'POST',
                    'url' => '/api/v1/servers/1/reconnect',
                ],
                'response' => [
                    'public_key' => 'secret',
                ],
                'expectedResult' => 'secret',
            ],
            [
                'method' => 'reactivate',
                'data' => $this->serverData(),
                'operation' => [
                    'method' => 'POST',
                    'url' => '/api/v1/servers/1/reactivate',
                ],
                'response' => [],
                'expectedResult' => true,
            ],
        ];
    }
}
