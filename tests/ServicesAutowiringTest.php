<?php

declare(strict_types=1);

namespace Luzrain\TelegramBotBundle\Test;

use Luzrain\TelegramBotApi\BotApi;
use Luzrain\TelegramBotApi\ClientApi;
use Luzrain\TelegramBotBundle\TelegramBot\Command\ButtonDeleteCommand;
use Luzrain\TelegramBotBundle\TelegramBot\Command\ButtonUpdateCommand;
use Luzrain\TelegramBotBundle\TelegramBot\Command\PolllingStartCommand;
use Luzrain\TelegramBotBundle\TelegramBot\Command\WebhookDeleteCommand;
use Luzrain\TelegramBotBundle\TelegramBot\Command\WebhookInfoCommand;
use Luzrain\TelegramBotBundle\TelegramBot\Command\WebhookUpdateCommand;
use Luzrain\TelegramBotBundle\TelegramBot\CommandDescriptionProcessor;
use Luzrain\TelegramBotBundle\TelegramBot\CommandMetadataProvider;
use Luzrain\TelegramBotBundle\TelegramBot\Controller\WebHookController;
use Luzrain\TelegramBotBundle\TelegramBot\LongPollingService;
use Luzrain\TelegramBotBundle\TelegramBot\UpdateHandler;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ServicesAutowiringTest extends KernelTestCase
{
    private static ContainerInterface $container;

    public function setUp(): void
    {
        self::$container = self::getContainer();
        self::$container->set('httpClient', $this->createMock(ClientInterface::class));
        self::$container->set('requestFactory', $this->createMock(RequestFactoryInterface::class));
        self::$container->set('streamFactory', $this->createMock(StreamFactoryInterface::class));
    }

    public function testServiceAutowiring(): void
    {
        $this->assertInstanceOf(BotApi::class, self::$container->get(BotApi::class));
        $this->assertInstanceOf(ClientApi::class, self::$container->get('telegram_bot.client_api'));
        $this->assertInstanceOf(WebHookController::class, self::$container->get('telegram_bot.webhook_controller'));
        $this->assertInstanceOf(LongPollingService::class, self::$container->get('telegram_bot.long_polling_service'));
        $this->assertInstanceOf(WebhookUpdateCommand::class, self::$container->get('telegram_bot.set_webhook_command'));
        $this->assertInstanceOf(WebhookInfoCommand::class, self::$container->get('telegram_bot.get_webhook_command'));
        $this->assertInstanceOf(WebhookDeleteCommand::class, self::$container->get('telegram_bot.delete_webhook_command'));
        $this->assertInstanceOf(PolllingStartCommand::class, self::$container->get('telegram_bot.polling_command'));
        $this->assertInstanceOf(ButtonUpdateCommand::class, self::$container->get('telegram_bot.menu_button_set_commands'));
        $this->assertInstanceOf(ButtonDeleteCommand::class, self::$container->get('telegram_bot.menu_button_delete_command'));
        $this->assertInstanceOf(UpdateHandler::class, self::$container->get('telegram_bot.update_handler'));
        $this->assertInstanceOf(CommandMetadataProvider::class, self::$container->get('telegram_bot.command_metadata_provider'));
        $this->assertInstanceOf(CommandDescriptionProcessor::class, self::$container->get('telegram_bot.description_processor'));
    }
}
