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
    Set,
    Str,
};
use function Innmind\Immutable\unwrap;
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set as DataSet,
};

class GithubTest extends TestCase
{
    use BlackBox;

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
            ->forAll(DataSet\Decorate::immutable(
                static fn($chars) => \implode('', $chars),
                DataSet\Sequence::of(
                    DataSet\Decorate::immutable(
                        static fn($ord) => \chr($ord),
                        DataSet\Integers::between(33, 126),
                    ),
                    DataSet\Integers::between(1, 50),
                ),
            ))
            ->then(function(string $user): void {
                $provide = new Github(
                    $http = $this->createMock(Transport::class),
                    $user
                );
                $http
                    ->expects($this->once())
                    ->method('__invoke')
                    ->with($this->callback(static function($request) use ($user): bool {
                        return $request->url()->toString() === "https://github.com/$user.keys" &&
                            $request->method()->toString() === 'GET';
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

                $this->assertInstanceOf(Set::class, $keys);
                $this->assertSame(PublicKey::class, (string) $keys->type());
                $this->assertCount(2, $keys);
                $keys = unwrap($keys);
                $this->assertSame('foo', \current($keys)->toString());
                \next($keys);
                $this->assertSame('bar', \current($keys)->toString());
            });
    }
}
