<?php
namespace App\Command\Utils;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncodingCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:password-encode';

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;

        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $encodedPassword = $this->encoder->encodePassword($user, $input->getArgument('password'));

        $output->write($encodedPassword);
        return 0;
    }
}
