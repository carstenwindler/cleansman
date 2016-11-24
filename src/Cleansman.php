<?php
/**
 * Cleansman
 *
 * A Codeception extension for cleaning up test output before you run new tests.
 */

namespace Codeception\Extension;

use Codeception\Configuration as Config;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class Cleansman extends \Codeception\Extension
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

        $fs = new Filesystem();
        $finder = new Finder();

        $finder->in($this->getLogDir());
        $fs->remove($finder);

        $this->writeln('done! (Removed ' . $finder->count() . ' files)');
    }


}
