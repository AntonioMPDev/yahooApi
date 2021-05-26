<?php


namespace App\Tests\feature;


use App\Entity\Stock;
use App\Tests\DatabaseDependantTestCase;
use App\Tests\DatabasePrimer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RefreshStockProfileCommandTest extends DatabaseDependantTestCase
{

    /** @test */
    public function the_refresh_stock_profile_command_behaves_correctly_when_a_stock_record_does_not_exist()
    {
        // Setup
        $application = new Application(self::$kernel);

        // command
        $command = $application->find('app:refresh-stock-profile');

        $commandTester = new CommandTester($command);

        // do something
        $commandTester->execute([
            'symbol' => 'AMRN',
            'region' => 'US'
        ]);

        // MAKE ASSERTIONS //
        // DB Assertions //
        $repo = $this->entityManager->getRepository(Stock::class);

        /** @var Stock $stock */
        $stock = $repo->findOneBy(['symbol' => 'AMRN']);

        $this->assertSame('USD', $stock->getCurrency());
        $this->assertSame('NasdaqGS', $stock->getExchangeName());
        $this->assertSame('AMRN', $stock->getSymbol());
        $this->assertSame('Amarin Corporation plc', $stock->getShortName());
        $this->assertSame('US', $stock->getRegion());
        $this->assertIsFloat( $stock->getPreviousClose());
        $this->assertIsFloat($stock->getPrice());



    }
}