<?php
/**
 * Cleansman
 *
 * A Codeception extension for cleaning up test output before you run new tests.
 */

namespace Codeception\Extension;

use Codeception\Exception\ExtensionException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class Cleansman extends \Codeception\Platform\Extension
{
    // list events to listen to
    static $events = array(
        'module.init' => 'moduleInit',
    );

    /**
     * Module Init
     */
    public function moduleInit(\Codeception\Event\SuiteEvent $e)
    {
        $this->write('Cleansman is cleaning up your mess...');

        $cleanupCounter = 0;
        $fs = new Filesystem();
        $finder = new Finder();

        $finder->in($this->getLogDir());

        foreach ($finder as $file) {
            try {
                $fs->remove($file);

                $cleanupCounter++;
            } catch (IOExceptionInterface $e) {
                $this->writeln('An error occurred while trying to remove ' . $e->getPath());
            }
        }

        $this->writeln('done! (Removed ' . $cleanupCounter . ' files)');
    }
}
