<?php

namespace Braincrafted\Bundle\BootstrapBundle\Command;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Util\VarUtils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class InstallCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('braincrafted:bootstrap:install')
            ->setDescription('Installs the icon font');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destDir = $this->getDestDir();

        $finder = new Finder;
        $fs = new Filesystem;

        try {
            $fs->mkdir($destDir);
        } catch (IOException $e) {
            $output->writeln(sprintf('<error>Could not create directory %s.</error>', $destDir));
            return;
        }

        $finder->files()->in($this->getSrcDir());

        foreach ($finder as $file) {
            $dest = sprintf('%s/%s', $destDir, $file->getBaseName());
            try {
                $fs->copy($file, $dest);
            } catch (IOException $e) {
                $output->writeln(sprintf('<error>Could not copy %s</error>', $file->getBaseName()));
                return;
            }
        }

        $output->writeln(sprintf('Copied Glyphicon fonts to <comment>%s</comment>.', $destDir));
    }

    /**
     * @return string
     */
    protected function getSrcDir()
    {
        return sprintf(
            '%s/../vendor/twbs/bootstrap/fonts',
            $this->getContainer()->getParameter('kernel.root_dir')
        );
    }

    /**
     * @return string
     */
    protected function getDestDir()
    {
        return sprintf(
            '%s/../web/fonts',
            $this->getContainer()->getParameter('kernel.root_dir')
        );
    }
}
