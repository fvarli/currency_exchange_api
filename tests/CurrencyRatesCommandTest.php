<?php

namespace App\Tests\Command;

use App\Command\CurrencyRatesCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CurrencyRatesCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        // (1) Create a new instance of the command
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:currency:rates');
        $commandTester = new CommandTester($command);

        // (2) Execute the command
        $commandTester->execute([
            'base_currency' => 'USD',
            'target_currencies' => ['EUR', 'GBP', 'JPY', 'TRY']
        ]);

        // (3) Check the output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Currency rates fetched and saved successfully!', $output);

        // Also check the return status
        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
