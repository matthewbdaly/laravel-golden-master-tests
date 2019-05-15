<?php

namespace Matthewbdaly\LaravelGoldenMasterTests;

use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

/**
 * Golden master test case
 */
class GoldenMasterTestCase extends BaseTestCase
{
    use CreatesApplication;

    public $baseUrl = 'http://localhost';

    protected $snapshotDir = "tests/snapshots/";

    protected $response;

    protected $path;

    /**
     * Go to page
     *
     * @param string $path Path to visit.
     * @return GoldenMasterTestCase
     */
    public function goto($path)
    {
        $this->path = $path;
        $this->response = $this->call('GET', $path);
        $this->assertNotEquals(404, $this->response->status());
        return $this;
    }

    /**
     * Save HTML
     *
     * @return GoldenMasterTestCase
     */
    public function saveHtml()
    {
        if (!$this->snapshotExists()) {
            $this->saveSnapshot();
        }
        return $this;
    }

    /**
     * Check snapshots match
     *
     * @return void
     */
    public function assertSnapshotsMatch()
    {
        $path = $this->getPath();
        $newHtml = $this->processHtml($this->getHtml());
        $oldHtml = $this->getOldHtml();
        $diff = "";
        if (function_exists('xdiff_string_diff')) {
            $diff = xdiff_string_diff($oldHtml, $newHtml);
        }
        $message = "The path $path does not match the snapshot\n$diff";
        self::assertThat($newHtml == $oldHtml, self::isTrue(), $message);
    }

    /**
     * Get HTML content
     *
     * @return string
     */
    protected function getHtml()
    {
        return $this->response->getContent();
    }

    /**
     * Get path
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * Get escaped path
     *
     * @return string
     */
    protected function getEscapedPath()
    {
        return $this->snapshotDir.str_replace('/', '_', $this->getPath()).'.snap';
    }

    /**
     * Does snapshot exist?
     *
     * @return boolean
     */
    protected function snapshotExists()
    {
        return file_exists($this->getEscapedPath());
    }

    /**
     * Process HTML
     *
     * @param string $html HTML to process.
     * @return string
     */
    protected function processHtml($html)
    {
        return preg_replace('/(<input type="hidden"[^>]+\>|<meta name="csrf-token" content="([a-zA-Z0-9]+)">)/i', '', $html);
    }

    /**
     * Save snapshot
     *
     * @return void
     */
    protected function saveSnapshot()
    {
        $html = $this->processHtml($this->getHtml());
        file_put_contents($this->getEscapedPath(), $html);
    }

    /**
     * Get old HTML
     *
     * @return string
     */
    protected function getOldHtml()
    {
        return file_get_contents($this->getEscapedPath());
    }
}
