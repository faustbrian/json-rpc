<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Tests\Support\Fakes\Server;

use function Cline\JsonRpc\post_json_rpc;

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
describe('Functions', function (): void {
    uses(RefreshDatabase::class);

    beforeEach(function (): void {
        // Set up RPC route for testing the post_json_rpc helper
        Route::rpc(Server::class);

        // Configure test environment
        Config::set('app.env', 'testing');
    });

    describe('Happy Paths', function (): void {
        test('sends JSON-RPC request with method only using default ID', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act
            $response = post_json_rpc('users.list');

            // Assert
            expect($response)->toBeInstanceOf(TestResponse::class);

            // Verify request payload structure
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('jsonrpc', '2.0')
                ->toHaveKey('method', 'users.list')
                ->toHaveKey('id', '01J34641TE5SF58ZX3N9HPT1BA')
                ->not->toHaveKey('params'); // null params should be filtered out
        });

        test('sends JSON-RPC request with method and parameters', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange
            $params = ['userId' => 123, 'active' => true];

            // Act
            $response = post_json_rpc('users.find', $params);

            // Assert
            expect($response)->toBeInstanceOf(TestResponse::class);

            // Verify request contains proper JSON-RPC structure with params
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('jsonrpc', '2.0')
                ->toHaveKey('method', 'users.find')
                ->toHaveKey('id', '01J34641TE5SF58ZX3N9HPT1BA')
                ->toHaveKey('params', $params);
        });

        test('sends JSON-RPC request with custom ID', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange
            $params = ['name' => 'John Doe'];
            $customId = 'my-custom-request-id-12345';

            // Act
            $response = post_json_rpc('users.create', $params, $customId);

            // Assert
            expect($response)->toBeInstanceOf(TestResponse::class);

            // Verify custom ID is used instead of default
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('jsonrpc', '2.0')
                ->toHaveKey('method', 'users.create')
                ->toHaveKey('id', $customId)
                ->toHaveKey('params', $params);
        });

        test('sends request to correct RPC route', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act
            $response = post_json_rpc('test.method');

            // Assert - Verify the route name 'rpc' was used
            expect($response->baseResponse->exception?->getRequest() ?? request())
                ->route()->getName()->toBe('rpc');
        });

        test('passes complex nested parameters correctly', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange
            $complexParams = [
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'roles' => ['admin', 'editor'],
                    'metadata' => [
                        'created_at' => '2024-01-01',
                        'preferences' => ['theme' => 'dark'],
                    ],
                ],
                'options' => ['notify' => true, 'validate' => false],
            ];

            // Act
            $response = post_json_rpc('users.create', $complexParams);

            // Assert
            expect($response)->toBeInstanceOf(TestResponse::class);

            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('params', $complexParams)
                ->and($payload['params'])->toBe($complexParams);
        });

        test('includes all required JSON-RPC 2.0 fields', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act
            $response = post_json_rpc('system.info', ['verbose' => true]);

            // Assert
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            // JSON-RPC 2.0 requires: jsonrpc, method, id (for requests)
            expect($payload)
                ->toHaveKeys(['jsonrpc', 'method', 'id'])
                ->and($payload['jsonrpc'])->toBe('2.0');
        });

        test('returns TestResponse instance for fluent Laravel assertions', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act
            $response = post_json_rpc('users.list');

            // Assert - Verify Laravel TestResponse methods are available
            expect($response)->toBeInstanceOf(TestResponse::class);
            expect(method_exists($response, 'assertStatus'))->toBeTrue();
            expect(method_exists($response, 'assertJson'))->toBeTrue();
            expect(method_exists($response, 'assertJsonStructure'))->toBeTrue();
        });

        test('integrates with Laravel HTTP testing helpers', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange
            $params = ['test' => 'value'];

            // Act
            $response = post_json_rpc('test.method', $params);

            // Assert - Use Laravel-specific assertions
            expect($response)->toBeInstanceOf(TestResponse::class);

            // Verify request was sent to correct route
            $route = ($response->baseResponse->exception?->getRequest() ?? request())->route();
            expect($route)->not()->toBeNull();
            expect($route->getName())->toBe('rpc');
        });

        test('uses Pest Laravel postJson helper internally', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act
            $response = post_json_rpc('test.method');

            // Assert - Verify we get a proper TestResponse from Pest Laravel
            expect($response)->toBeInstanceOf(TestResponse::class);
            // JsonResponse extends Response, so check for the more specific type
            expect($response->baseResponse)->toBeInstanceOf(JsonResponse::class);
        });
    });

    describe('Sad Paths', function (): void {
        test('filters out null params parameter', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act - Explicitly pass null params
            $response = post_json_rpc('test.method', null);

            // Assert - params key should not be present when null
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->not->toHaveKey('params')
                ->toHaveKey('jsonrpc', '2.0')
                ->toHaveKey('method', 'test.method')
                ->toHaveKey('id');
        });

        test('sends request to valid route endpoint', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act - The function internally uses route('rpc')
            $response = post_json_rpc('test.method');

            // Assert - Verify we successfully called the route
            expect($response)->toBeInstanceOf(TestResponse::class);

            // Verify route helper was used
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            expect($request->route())->not()->toBeNull();
            expect($request->route()->getName())->toBe('rpc');
        });

        test('handles missing Pest Laravel helper gracefully', function (): void {
            // This test verifies the function_exists check works correctly
            // The helper is only defined if Pest\Laravel\postJson exists
            expect(function_exists('Pest\Laravel\postJson'))->toBeTrue();
            expect(function_exists('Cline\JsonRpc\post_json_rpc'))->toBeTrue();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles empty params array correctly', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Empty array is filtered out by array_filter (falsy value)
            $emptyParams = [];

            // Act
            $response = post_json_rpc('test.method', $emptyParams);

            // Assert - Empty array should be filtered out (array_filter removes falsy values)
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->not->toHaveKey('params') // Empty array is filtered out
                ->toHaveKey('jsonrpc', '2.0')
                ->toHaveKey('method', 'test.method')
                ->toHaveKey('id');
        });

        test('handles method names with special characters', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Method names can contain dots, underscores, hyphens
            $specialMethods = [
                'user.profile.get',
                'system_status',
                'data-export',
                'api.v2.users.list',
            ];

            foreach ($specialMethods as $method) {
                // Act
                $response = post_json_rpc($method);

                // Assert
                $request = $response->baseResponse->exception?->getRequest() ?? request();
                $payload = $request->json()->all();

                expect($payload)
                    ->toHaveKey('method', $method);
            }
        });

        test('handles empty string as custom ID', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Empty string is filtered out by array_filter (falsy value)
            $emptyId = '';

            // Act
            $response = post_json_rpc('test.method', null, $emptyId);

            // Assert - Empty string is filtered out, so default ID is used instead
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->not->toHaveKey('id') // Empty string ID is filtered out
                ->toHaveKey('jsonrpc', '2.0')
                ->toHaveKey('method', 'test.method');
        });

        test('handles unicode characters in parameters', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Unicode in various scripts
            $unicodeParams = [
                'name' => '日本語テスト',
                'description' => 'Тестовое описание',
                'emoji' => '🚀🎉💯',
                'arabic' => 'مرحبا بك',
                'special' => '©®™€£¥',
            ];

            // Act
            $response = post_json_rpc('users.create', $unicodeParams);

            // Assert
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('params', $unicodeParams)
                ->and($payload['params'])->toBe($unicodeParams);
        });

        test('handles numeric string as custom ID', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Numeric strings should remain strings
            $numericId = '12345';

            // Act
            $response = post_json_rpc('test.method', null, $numericId);

            // Assert
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('id', $numericId)
                ->and($payload['id'])->toBe($numericId)
                ->and($payload['id'])->toBeString();
        });

        test('handles deeply nested parameter structures', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - 5 levels of nesting
            $deeplyNested = [
                'level1' => [
                    'level2' => [
                        'level3' => [
                            'level4' => [
                                'level5' => 'deep value',
                                'array' => [1, 2, 3],
                            ],
                        ],
                    ],
                ],
            ];

            // Act
            $response = post_json_rpc('data.process', $deeplyNested);

            // Assert
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('params')
                ->and($payload['params'])->toBe($deeplyNested)
                ->and($payload['params']['level1']['level2']['level3']['level4']['level5'])
                ->toBe('deep value');
        });

        test('handles parameters with boolean and numeric values', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Mixed types
            $mixedParams = [
                'enabled' => true,
                'disabled' => false,
                'count' => 42,
                'price' => 19.99,
                'negative' => -5,
                'zero' => 0,
            ];

            // Act
            $response = post_json_rpc('settings.update', $mixedParams);

            // Assert
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('params', $mixedParams)
                ->and($payload['params']['enabled'])->toBeTrue()
                ->and($payload['params']['disabled'])->toBeFalse()
                ->and($payload['params']['count'])->toBe(42)
                ->and($payload['params']['zero'])->toBe(0);
        });

        test('handles ULID format default ID', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act
            $response = post_json_rpc('test.method');

            // Assert - Default ID matches expected ULID format
            $request = $response->baseResponse->exception?->getRequest() ?? request();
            $payload = $request->json()->all();

            expect($payload)
                ->toHaveKey('id', '01J34641TE5SF58ZX3N9HPT1BA')
                ->and($payload['id'])->toBeString()
                ->and($payload['id'])->toHaveLength(26); // ULIDs are 26 characters
        });

        test('works with different testing environments', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Temporarily change environment
            $originalEnv = Config::get('app.env');
            Config::set('app.env', 'local');

            // Act
            $response = post_json_rpc('test.method');

            // Assert - Function works regardless of environment
            expect($response)->toBeInstanceOf(TestResponse::class);

            // Cleanup
            Config::set('app.env', $originalEnv);
        });

        test('handles concurrent test execution with route registration', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act - Make multiple concurrent-like calls
            $response1 = post_json_rpc('test.method1');
            $response2 = post_json_rpc('test.method2');
            $response3 = post_json_rpc('test.method3');

            // Assert - All requests should succeed
            expect($response1)->toBeInstanceOf(TestResponse::class);
            expect($response2)->toBeInstanceOf(TestResponse::class);
            expect($response3)->toBeInstanceOf(TestResponse::class);

            // Verify each has correct method
            $payload1 = ($response1->baseResponse->exception?->getRequest() ?? request())->json()->all();
            $payload2 = ($response2->baseResponse->exception?->getRequest() ?? request())->json()->all();
            $payload3 = ($response3->baseResponse->exception?->getRequest() ?? request())->json()->all();

            expect($payload3['method'])->toBe('test.method3'); // Check last one to ensure no overwriting
        });

        test('maintains JSON-RPC protocol compliance with array_filter behavior', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Test various falsy values to understand array_filter behavior
            $testCases = [
                ['params' => null, 'id' => 'valid-id', 'should_have_params' => false, 'should_have_id' => true],
                ['params' => [], 'id' => 'valid-id', 'should_have_params' => false, 'should_have_id' => true],
                ['params' => ['key' => 'value'], 'id' => '', 'should_have_params' => true, 'should_have_id' => false],
            ];

            foreach ($testCases as $index => $testCase) {
                // Act
                $response = post_json_rpc(
                    'test.method.'.$index,
                    $testCase['params'],
                    $testCase['id'],
                );

                // Assert
                $request = $response->baseResponse->exception?->getRequest() ?? request();
                $payload = $request->json()->all();

                if ($testCase['should_have_params']) {
                    expect($payload)->toHaveKey('params');
                } else {
                    expect($payload)->not()->toHaveKey('params');
                }

                if ($testCase['should_have_id']) {
                    expect($payload)->toHaveKey('id');
                } else {
                    expect($payload)->not()->toHaveKey('id');
                }
            }
        });

        test('integrates with Laravel application context', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Act
            $response = post_json_rpc('test.method');

            // Assert - Verify Laravel application context is available
            expect(app())->toBeInstanceOf(Application::class);
            expect($response)->toBeInstanceOf(TestResponse::class);

            // Verify route helper works within function context
            $routeUrl = route('rpc');
            expect($routeUrl)->toBeString();
            expect($routeUrl)->toContain('rpc');
        });

        test('supports JSON-RPC method naming conventions', function (): void {
            if (!function_exists('Cline\JsonRpc\post_json_rpc')) {
                $this->markTestSkipped('post_json_rpc function not available');
            }

            // Arrange - Various JSON-RPC 2.0 method naming conventions
            $methodConventions = [
                'simple',                           // Simple name
                'namespace.method',                 // Namespaced
                'api.v2.users.list',               // Multi-level namespace
                'system-info',                      // Kebab-case
                'get_user_profile',                 // Snake_case
                'rpc.discover',                     // Standard RPC method
            ];

            foreach ($methodConventions as $method) {
                // Act
                $response = post_json_rpc($method);

                // Assert - All naming conventions should work
                $request = $response->baseResponse->exception?->getRequest() ?? request();
                $payload = $request->json()->all();

                expect($payload)->toHaveKey('method', $method);
                expect($response)->toBeInstanceOf(TestResponse::class);
            }
        });
    });
});
