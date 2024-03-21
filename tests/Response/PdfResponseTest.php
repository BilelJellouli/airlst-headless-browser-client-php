<?php

declare(strict_types=1);

namespace Airlst\HeadlessBrowserClient\Tests\Response;

use Airlst\HeadlessBrowserClient\Response\PdfResponse;
use Airlst\HeadlessBrowserClient\Response\Response;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * @internal
 */
final class PdfResponseTest extends TestCase
{
    public function testPdfMethodReturnsBodyItemFromResponse(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('getBody->getContents')->andReturn(json_encode(['pdf' => base64_encode('pdf content')]));

        $pdf = new PdfResponse($response);

        $this->assertSame('pdf content', $pdf->contents());
    }

    public function testThrowsRuntimeExceptionOnInvalidBase64EncodedContent(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('getBody->getContents')->andReturn(json_encode(['pdf' => 'not-encoded']));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to decode PDF contents.');

        (new PdfResponse($response))->contents();
    }

    public function testSubclassFromResponse(): void
    {
        $this->assertTrue(
            is_subclass_of(PdfResponse::class, Response::class)
        );
    }
}
