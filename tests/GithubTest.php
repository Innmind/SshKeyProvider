<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Github,
    Provide,
    PublicKey,
};
use Innmind\HttpTransport\Transport;
use Innmind\Http\Message\Response;
use Innmind\Stream\Readable;
use Innmind\Immutable\{
    SetInterface,
    Str,
};
use PHPUnit\Framework\TestCase;
use Eris\{
    Generator,
    TestTrait,
};

class GithubTest extends TestCase
{
    use TestTrait;

    public function testInterface()
    {
        $this->assertInstanceOf(
            Provide::class,
            new Github(
                $this->createMock(Transport::class),
                'foo'
            )
        );
    }

    public function testThrowWhenEmptyName()
    {
        $this->expectException(\DomainException::class);

        new Github(
            $this->createMock(Transport::class),
            ''
        );
    }

    public function testInvokation()
    {
        $this
            ->forAll(Generator\string())
            ->when(static function(string $user): bool {
                return $user !== '';
            })
            ->then(function(string $user): void {
                $provide = new Github(
                    $http = $this->createMock(Transport::class),
                    $user
                );
                $http
                    ->expects($this->once())
                    ->method('__invoke')
                    ->with($this->callback(static function($request) use ($user): bool {
                        return (string) $request->url() === "https://github.com/$user.keys" &&
                            (string) $request->method() === 'GET';
                    }))
                    ->willReturn($response = $this->createMock(Response::class));
                $response
                    ->expects($this->once())
                    ->method('body')
                    ->willReturn($body = $this->createMock(Readable::class));
                $body
                    ->expects($this->once())
                    ->method('read')
                    ->willReturn(Str::of(<<<KEYS
foo
bar

KEYS
                    ));

                $keys = $provide();

                $this->assertInstanceOf(SetInterface::class, $keys);
                $this->assertSame(PublicKey::class, (string) $keys->type());
                $this->assertCount(2, $keys);
                $this->assertSame('foo', (string) $keys->current());
                $keys->next();
                $this->assertSame('bar', (string) $keys->current());
            });
    }
}
