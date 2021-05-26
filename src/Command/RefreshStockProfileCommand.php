<?php

namespace App\Command;

use App\Entity\Stock;
use App\Http\FinanceApiClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;

class RefreshStockProfileCommand extends Command
{
    protected static $defaultName = 'app:refresh-stock-profile';

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var FinanceApiClientInterface
     */
    private FinanceApiClientInterface $financeApiClient;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;


    public function __construct(
        EntityManagerInterface $entityManager,
        FinanceApiClientInterface $financeApiClient,
        SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;

        $this->financeApiClient = $financeApiClient;

        $this->serializer = $serializer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Retrieve a sock profile from the yahoo finance API. Update the record in the DB')
            ->addArgument('symbol', InputArgument::REQUIRED, 'Stock symbol e.g. AMRN for Amazon')
            ->addArgument('region', InputArgument::REQUIRED, 'The region of the company e.g US')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 1. Ping yahoo API and grab the response (a stock profile)
        $stockProfile = $this->financeApiClient->fetchStockProfile(
            $input->getArgument('symbol'),
            $input->getArgument('region'));

        // Handle non 200 status code responses
        if ($stockProfile['statusCode'] !== 200){

        }
        
        // 2c. Use the stock profile to create a record if it doesn't exist
        $stock = $this->serializer->deserialize($stockProfile['content'], Stock::class, 'json');

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
