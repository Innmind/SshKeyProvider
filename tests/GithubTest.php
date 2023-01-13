<?php
declare(strict_types = 1);

namespace Tests\Innmind\SshKeyProvider;

use Innmind\SshKeyProvider\{
    Github,
    Provide,
    PublicKey,
};
use Innmind\HttpTransport\{
    Transport,
    Success,
};
use Innmind\Http\Message\{
    Response,
    Request,
    StatusCode,
};
use Innmind\Filesystem\File\Content;
use Innmind\Immutable\{
    Set,
    Either,
};
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
            Github::of(
                $this->createMock(Transport::class),
                'foo',
            ),
        );
    }

    public function testThrowWhenEmptyName()
    {
        $this->expectException(\DomainException::class);

        Github::of(
            $this->createMock(Transport::class),
            '',
        );
    }

    public function testInvokation()
    {
        $this
            ->forAll(DataSet\Strings::madeOf(DataSet\Chars::alphanumerical())->between(1, 50))
            ->then(function(string $user): void {
                $provide = Github::of(
                    $http = $this->createMock(Transport::class),
                    $user,
                );
                $response = $this->createMock(Response::class);
                $response
                    ->expects($this->once())
                    ->method('statusCode')
                    ->willReturn(StatusCode::ok);
                $http
                    ->expects($this->once())
                    ->method('__invoke')
                    ->with($this->callback(static function($request) use ($user): bool {
                        return $request->url()->toString() === "https://github.com/$user.keys" &&
                            $request->method()->toString() === 'GET';
                    }))
                    ->willReturn(Either::right(new Success(
                        $this->createMock(Request::class),
                        $response,
                    )));
                $response
                    ->expects($this->once())
                    ->method('body')
                    ->willReturn(Content\Lines::ofContent(<<<KEYS
                    foo
                    bar

                    KEYS));

                $keys = $provide();

                $this->assertInstanceOf(Set::class, $keys);
                $this->assertCount(2, $keys);
                $keys = $keys->toList();
                $this->assertSame('foo', \current($keys)->toString());
                \next($keys);
                $this->assertSame('bar', \current($keys)->toString());
            });
    }
}
