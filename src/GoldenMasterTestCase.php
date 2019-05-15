<?php

namespace Matthewbdaly\LaravelGoldenMasterTests;

use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

class GoldenMasterTestCase extends BaseTestCase
{
    use CreatesApplication;

    public $baseUrl = 'http://localhost';

    protected $snapshotDir = "tests/snapshots/";

    protected $response;

    protected $path;

    public function goto($path)
    {
        $this->path = $path;
        $this->response = $this->call('GET', $path);
        $this->assertNotEquals(404, $this->response->status());
        return $this;
    }

    public function saveHtml()
    {
        if (!$this->snapshotExists()) {
            $this->saveSnapshot();
        }
        return $this;
    }

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

    protected function getHtml()
    {
        return $this->response->getContent();
    }

    protected function getPath()
    {
        return $this->path;
    }

    protected function getEscapedPath()
    {
        return $this->snapshotDir.str_replace('/', '_', $this->getPath()).'.snap';
    }

    protected function snapshotExists()
    {
        return file_exists($this->getEscapedPath());
    }

    protected function processHtml($html)
    {
        return preg_replace('/(<input type="hidden"[^>]+\>|<meta name="csrf-token" content="([a-zA-Z0-9]+)">)/i', '', $html);
    }

    protected function saveSnapshot()
    {
        $html = $this->processHtml($this->getHtml());
        file_put_contents($this->getEscapedPath(), $html);
    }

    protected function getOldHtml()
    {
        return file_get_contents($this->getEscapedPath());
    }
}
