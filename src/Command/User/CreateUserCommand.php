<?php
namespace App\Command\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:create';

    private $entityManager;
    private $encoder;

    public function __construct(EntityManagerInterface $entityManager,
                                UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'User identifier (email)');
        $this->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $encodedPassword = $this->encoder->encodePassword($user, $input->getArgument('password'));

        $user->setEmail($input->getArgument('username'));
        $user->setPassword($encodedPassword);
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();


        $output->writeln($user->getUsername() . ' Created !');
        return 0;
    }
}
