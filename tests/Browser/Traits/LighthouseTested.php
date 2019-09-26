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

        $lighthouse = new Process([
            'lighthouse-ci',
            $pageUrl,
            '--port=9222',
            '--seo=100',
            '--accessibility=100',
            '--best-practices=100',
        ]);

        $lighthouse->run();

        file_put_contents($reportPath, $lighthouse->getOutput());

        if (!$lighthouse->isSuccessful()) {
            Assert::fail('lighthouse test failed on ' . $this->url() . ":\n" . $lighthouse->getOutput());
        }
    }
}
