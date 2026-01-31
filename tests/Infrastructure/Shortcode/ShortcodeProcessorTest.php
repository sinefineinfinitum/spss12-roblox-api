<?php

namespace SineFine\RobloxApi\Tests\Infrastructure\Shortcode;

use PHPUnit\Framework\TestCase;
use SineFine\RobloxApi\Data\Args\ArgumentSpecification;
use SineFine\RobloxApi\Data\Source\IDataSource;
use SineFine\RobloxApi\Infrastructure\Shortcode\ShortcodeProcessor;

class ShortcodeProcessorTest extends TestCase
{
    private ShortcodeProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new ShortcodeProcessor();
    }

    public function testProcessReturnsResultOnSuccess(): void
    {
        $dataSource = $this->createMock(IDataSource::class);
        $argSpec = new ArgumentSpecification(['UserId']);
        
        $dataSource->method('getArgumentSpecification')->willReturn($argSpec);
        $dataSource->expects($this->once())
            ->method('exec')
            ->with(['123'], [])
            ->willReturn('Success Result');

        $attrs = ['userid' => '123'];
        $result = $this->processor->process($dataSource, $attrs);

        $this->assertEquals('Success Result', $result);
    }

    public function testProcessMapsArgumentsCorrectly(): void
    {
        $dataSource = $this->createMock(IDataSource::class);
        $argSpec = new ArgumentSpecification(['UserId']);
        
        $dataSource->method('getArgumentSpecification')->willReturn($argSpec);
        $dataSource->expects($this->once())
            ->method('exec')
            ->with(['456'], [])
            ->willReturn('Success');

        // Test with mapped key 'user_id'
        $attrs = ['user_id' => '456'];
        $result = $this->processor->process($dataSource, $attrs);

        $this->assertEquals('Success', $result);
    }

    public function testProcessHandlesMissingRequiredArgument(): void
    {
        $dataSource = $this->createMock(IDataSource::class);
        $argSpec = new ArgumentSpecification(['UserId']);
        
        $dataSource->method('getArgumentSpecification')->willReturn($argSpec);
        
        $attrs = ['wrong_attr' => '123'];
        $result = $this->processor->process($dataSource, $attrs);

        $this->assertStringContainsString("Error: Required argument 'UserId' (attr: 'userid') is missing", $result);
    }

    public function testProcessHandlesOptionalArguments(): void
    {
        $dataSource = $this->createMock(IDataSource::class);
        $argSpec = new ArgumentSpecification(['UserId'], ['format' => 'String']);
        
        $dataSource->method('getArgumentSpecification')->willReturn($argSpec);
        $dataSource->expects($this->once())
            ->method('exec')
            ->with(['123'], ['format' => 'png'])
            ->willReturn('Success');

        $attrs = ['userid' => '123', 'format' => 'png'];
        $result = $this->processor->process($dataSource, $attrs);

        $this->assertEquals('Success', $result);
    }

    public function testProcessReturnsJsonForArrays(): void
    {
        $dataSource = $this->createMock(IDataSource::class);
        $argSpec = new ArgumentSpecification(['UserId']);
        
        $dataSource->method('getArgumentSpecification')->willReturn($argSpec);
        $dataSource->method('exec')->willReturn(['status' => 'ok']);

        $attrs = ['userid' => '123'];
        $result = $this->processor->process($dataSource, $attrs);

        $this->assertJson($result);
        $this->assertStringContainsString('"status": "ok"', $result);
    }

    public function testProcessHandlesExceptions(): void
    {
        $dataSource = $this->createMock(IDataSource::class);
        $argSpec = new ArgumentSpecification(['UserId']);
        
        $dataSource->method('getArgumentSpecification')->willReturn($argSpec);
        $dataSource->method('exec')->willThrowException(new \Exception("Something went wrong"));

        $attrs = ['userid' => '123'];
        $result = $this->processor->process($dataSource, $attrs);

        $this->assertEquals('Error: Something went wrong', $result);
    }
}
