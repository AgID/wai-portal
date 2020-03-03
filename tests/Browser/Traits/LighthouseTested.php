<?php

namespace Tests\Browser\Traits;

use Illuminate\Support\Str;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\Process;

/**
 * Lighthouse testing.
 */
trait LighthouseTested
{
    /**
     * Perform a browser test with google lighthouse.
     */
    public function lighthouseTest()
    {
        $pageUrl = config('app.url') . $this->url();
        $reportPath = base_path('tests/Browser/lighthouse') . '/' . 'report-' . Str::slug($pageUrl);

        if (file_exists($reportPath)) {
            return;
        }

        $lighthouseCollect = new Process([
            'lhci',
            'collect',
            '--config',
            base_path() . '/lighthouserc.json',
            '--url',
            $pageUrl,
        ]);
        $lighthouseCollect->setWorkingDirectory(base_path('tests/Browser/lighthouse'));

        $lighthouseAssert = new Process([
            'lhci',
            'assert',
            '--config',
            base_path() . '/lighthouserc.json',
        ]);
        $lighthouseAssert->setWorkingDirectory(base_path('tests/Browser/lighthouse'));

        $lighthouseCollect->run();
        $lighthouseAssert->run();

        file_put_contents($reportPath, $lighthouseCollect->getOutput());
        file_put_contents($reportPath, $lighthouseCollect->getErrorOutput(), FILE_APPEND);
        file_put_contents($reportPath, $lighthouseAssert->getOutput(), FILE_APPEND);
        file_put_contents($reportPath, $lighthouseAssert->getErrorOutput(), FILE_APPEND);

        if (!$lighthouseAssert->isSuccessful()) {
            Assert::fail('lighthouse test failed on ' . $this->url() . ":\n" . $lighthouseAssert->getErrorOutput());
        }
    }
}
