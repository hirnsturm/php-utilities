<?php

namespace Sle\Logger;

/**
 * Logger
 *
 * This is implemented logger according to psr-standard
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 * @copyright (c) 2013, Steve Lenz
 */
class Logger
{
    /**
     * Base file for logs
     *
     * @var string
     */
    private $defaultFilename = null;

    /**
     * @var string
     */
    private $name = null;

    /**
     * @var \DateTimeZone
     */
    private $timezone;

    /**
     * @var string
     */
    private $logDir = null;

    /**
     * Constructor
     *
     * @param string $name The logging channel
     * @param string $filename The log filename (e.g. my_dev.log)
     */
    public function __construct($name, $filename)
    {
        $this->name = $name;
        $this->defaultFilename = $filename;
        $this->timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        $this->logDir = __DIR__ . '/../../../../logs/';
    }

    /**
     * Success
     *
     * @param string $message
     * @param array $context
     */
    public function success($message, array $context = array())
    {
        $this->log('SUCCESS', $message, $context);
    }

    /**
     * Info
     *
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Warning
     *
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Error
     *
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Debug
     *
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Build the log message
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    private function buildRecord($level, $message, array $context = array())
    {
        $datetime = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)),
            $this->timezone)->setTimezone($this->timezone);

        $record = array(
            'datetime' => '[' . $datetime->format('Y-m-d H:i:s') . ']',
            'channel'  => $this->name . '.' . $level . ':',
            'message'  => (string)$message,
            'context'  => @json_encode($context),
        );

        return implode(' ', $record) . "\n";
    }

    /**
     * Stores data in named file
     *
     * @param string $string
     * @param string $mode - Mode for php fopen
     * @return bool
     */
    private function write($string, $mode = 'a+')
    {
        $this->isDir(dirname($this->logDir . $this->defaultFilename));

        if (!$handle = fopen($this->logDir . $this->defaultFilename, $mode)) {
            return false;
        }

        fwrite($handle, $string);
        fclose($handle);

        $this->fixPermissions();

        return true;
    }

    /**
     * Checks whether the given directory exists. If not then the directory will created.
     *
     * @param string $dirName
     * @param int $mode
     * @param bool $recursive
     */
    private function isDir($dirName, $mode = 0777, $recursive = true)
    {
        if (!is_dir($dirName)) {
            mkdir($dirName, $mode, $recursive);
        }
    }

    /**
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function log($level, $message, array $context = array())
    {
        $this->write($this->buildRecord($level, $message, $context));
    }

    /**
     * Fixes file permissions
     */
    private function fixPermissions()
    {
        chmod($this->logDir . $this->defaultFilename, 0770);
    }

}
