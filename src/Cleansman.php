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
        'suite.after' => 'suiteAfter'
    );

    public static $garbageCans = [];

    public static function throwAway($garbageCan, $path)
    {
        if (!isset(self::$garbageCans[$garbageCan])) {
            self::$garbageCans[$garbageCan] = [];
        }

        self::$garbageCans[$garbageCan][] = $path;
    }

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

    public function suiteAfter()
    {
        if (count(self::$garbageCans) == 0) {
            return;
        }

        $this->write('Cleansman is emptying your gargabe cans...');

        $cleanupCounter = 0;
        $fs = new Filesystem();
        $finder = new Finder();

        foreach (self::$garbageCans as $garbageCan => $garbage) {
            foreach ($garbage as $fileToThrowAway) {
                $finder->files()->name($fileToThrowAway)->in($garbageCan);

                foreach ($finder as $file) {
                    $fs->remove($file);
                    $cleanupCounter++;
                }
            }
        }

        $this->writeln('done! (Removed ' . $cleanupCounter . ' files from '
            . count(self::$garbageCans) . ' garbage cans)');
    }
}
