<?php

namespace Tests\TestCase\Action;

use App\Entity\Issue;
use App\Service\IssueService;
use Exception;
use PHPUnit\Framework\TestCase;
use Tests\Traits\AppTestTrait;

class GetIssueActionTest extends TestCase
{
    use AppTestTrait;

    /**
     * Test.
     *
     * @dataProvider provideIssueReaderAction
     *
     * @param array $issue
     * @param array $expected The expected result
     *
     * @return void
     */
    public function testIssueGetActionOK(array $issue, array $expected): void
    {
        // Mock the repository resultset
        $this->mock(IssueService::class,'issue_service')
            ->method('get')->willReturn($issue);

        // Create request with method and url
        $request = $this->createRequest('GET', '/issue/10');

        // Make request and fetch response
        $response = $this->app->handle($request);
        
        // Asserts
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonData($expected, $response);
    }

    /**
     * Test.
     * @return void
     */
    public function testIssueGetAction404(): void
    {
        
        // Create request with method and url
        $request = $this->createRequest('GET', '/issue/1000');

        // Make request and fetch response
        $response = $this->app->handle($request);
        
        // Asserts
        $this->assertSame(404, $response->getStatusCode());
        
    }

    /**
     * Test.
     * @return void
     */
    public function testIssueGetActionServerError(): void
    {
        $this->mock(IssueService::class,'issue_service')
            ->method('get')->will($this->throwException(new Exception()));
        // Create request with method and url
        $request = $this->createRequest('GET', '/issue/1000');

        // Make request and fetch response
        $response = $this->app->handle($request);
        
        // Asserts
        $this->assertSame(500, $response->getStatusCode());
    }

    /**
     * Test.
     *
     * @dataProvider provideIssueReveal
     *
     * @param array $issue
     * @param array $expected The expected result
     *
     * @return void
     */
    public function testIssueGetActionReveal(array $issue, array $expected): void
    {
        // Mock the repository resultset
        $this->mock(IssueService::class,'issue_service')
            ->method('get')->willReturn($issue);

        // Create request with method and url
        $request = $this->createRequest('GET', '/issue/10');

        // Make request and fetch response
        $response = $this->app->handle($request);
        
        // Asserts
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJsonData($expected, $response);
    }

    /**
     * Provider.
     *
     * @return array The data
     */
    public function provideIssueReaderAction(): array    
    {
        $issue = new Issue();
        $issue->setCode('10');
        $issue->setStatus('waiting');
        $issue->setMembers([
            [
                'name'=>'abcd',
                'status' => 'waiting'
            ]
        ]);
        
        return [
            'data'=>[
                $issue->toArray(),[
                    'code' => '10',
                    'status' => 'waiting',
                    'members' => [
                        [
                            'name'=>'abcd',
                            'status' => 'waiting'
                        ]
                    ],
                    'avg' => 0
                ]
            ]
        ];
    }

    /**
     * Provider.
     *
     * @return array The data
     */
    public function provideIssueReveal(): array    
    {
        $issue = new Issue();
        $issue->setCode('1');
        $issue->setStatus('reveal');
        $issue->setMembers([
            [
                'name'=>'abcd',
                'status' => 'voted',
                'value' => 8,
            ],
            [
                'name'=>'xyja',
                'status' => 'voted',
                'value' => 14,
            ],
            [
                'name'=>'another',
                'status' => 'passed',
                'value' => 0,
            ]
        ]);
        $issue->setAvg(11);
        
        return [
            'data'=>[
                $issue->toArray(),[
                    'code' => '1',
                    'status' => 'reveal',
                    'members' => [
                        [
                            'name'=>'abcd',
                            'status' => 'voted',
                            'value' => 8,
                        ],
                        [
                            'name'=>'xyja',
                            'status' => 'voted',
                            'value' => 14,
                        ],
                        [
                            'name'=>'another',
                            'status' => 'passed',
                            'value' => 0,
                        ]
                    ],
                    'avg' => 11
                ]
            ]
        ];
    }
}