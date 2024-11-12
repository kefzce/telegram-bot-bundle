<?php

declare(strict_types=1);

namespace Luzrain\TelegramBotBundle;

use Luzrain\TelegramBotApi\Exception\TelegramTypeException;
use Luzrain\TelegramBotApi\Type\Update;
use Luzrain\TelegramBotBundle\Event\BeforeSend;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

final readonly class WebHookController
{
    public function __construct(
        private UpdateHandler $updateHandler,
        private string|null $secretToken,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if ($request->getMethod() !== 'POST') {
            throw new MethodNotAllowedHttpException(['POST'], 'Method Not Allowed');
        }

        if ($this->secretToken !== null && $request->headers->get('X-Telegram-Bot-Api-Secret-Token') !== $this->secretToken) {
            throw new AccessDeniedHttpException('Access denied');
        }

        try {
            $update = Update::fromJson($request->getContent());
            $this->dispatcher->dispatch($update);
        } catch (TelegramTypeException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $object = $this->updateHandler->handle($update);
        $this->dispatcher->dispatch(new BeforeSend($object));
        $response = new JsonResponse($object);
        $response->headers->set('Content-Length', (string) \strlen((string) $response->getContent()));

        return $response;
    }
}
