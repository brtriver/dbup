<?php
namespace Dbup\Util;

use Symfony\Component\Finder\Finder;

class Compiler
{
    public function compile($pharFile = 'dbup.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'dbup.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        // CLI Component files
        foreach ($this->getFiles() as $file) {
            $phar->addFromString($file->getPathName(), file_get_contents($file));
        }

        $this->addDbup($phar);

        // Stubs
        $phar->setStub($this->getStub());
        $phar->stopBuffering();

        unset($phar);
        chmod($pharFile, 0777);
    }

    /**
     * Remove the shebang from the file before add it to the PHAR file.
     *
     * @param \Phar $phar PHAR instance
     */
    protected function addDbup(\Phar $phar)
    {
        $content = file_get_contents(__DIR__ . '/../../../dbup');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);

        $phar->addFromString('dbup', $content);
    }

    protected function getStub()
    {
        return <<<EOL
#!/usr/bin/env php
<?php
Phar::mapPhar('dbup.phar');
require 'phar://dbup.phar/dbup';
__HALT_COMPILER();
EOL;

    }

    protected function getFiles()
    {
        $finder = new Finder();
        $srcIterator = $finder->files()->exclude('Tests')->name('*.php')->in(array('vendor', 'src'));

        return iterator_to_array($srcIterator);
    }
}