<?php

namespace Sle\Utilities\Security;

/**
 * Session
 *
 * This class offers an easy to use session layer for working with PHP-Sessions.
 *
 * @author Steve Lenz <kontakt@steve-lenz.de>
 */
class Session
{

    /**
     * @var null
     */
    static private $instance = null;

    /**
     *
     */
    public function __construct()
    {
        $this->getInstance();

        $this->generateToken();

        return self::$instance;
    }

    /**
     * Init session
     *
     * @return boolean
     */
    private function getInstance()
    {
        if (version_compare(phpversion(), '5.4.0') >= 0) {
            $status = session_status();
            switch ($status) {
                case PHP_SESSION_NONE:
                    self::$instance = session_start();
                    $this->regenerateSessionId();
                    break;
                case PHP_SESSION_ACTIVE:
                    self::$instance = true;
                    break;
                case PHP_SESSION_DISABLED:
                    false;
                    break;
                default:
                    false;
            }
        } else {
            if (!isset($_SESSION)) {
                self::$instance = session_start();
                $this->regenerateSessionId();
            } else {
                self::$instance = true;
            }
        }
    }

    /**
     * Regenerate the session ID
     */
    private function regenerateSessionId()
    {
        session_regenerate_id();
    }

    /**
     * Returns session id
     *
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Returns session name
     *
     * @return string
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * Add data into session
     *
     * @param string $key
     * @param mixed $data
     * @return $this
     */
    public function set($key, $data)
    {
        if ($key !== null) {
            $_SESSION[$key] = $data;
        }

        return $this;
    }

    /**
     * Get data from session
     *
     * @param string $propertyName
     * @return null
     */
    public function get($propertyName = null)
    {
        if ($propertyName === null) {
            return $_SESSION;
        }

        if (array_key_exists($propertyName, $_SESSION)) {
            return $_SESSION[$propertyName];
        }

        return null;
    }

    /**
     * Clean up session
     *
     * @param mixed $key
     * @return $this
     */
    public function cleanUp($key)
    {
        if (!is_array($key)) {
            $key = array($key);
        }

        foreach ($key as $item) {
            unset($_SESSION[$item]);
        }

        return $this;
    }

    /**
     * Removes all data from session
     */
    public function clear()
    {
        $_SESSION = array();

        return $this;
    }

    /**
     * Destroy session
     */
    public function destroy()
    {
        session_destroy();
    }

    /**
     * Add a flash message by key
     *
     * @param string $key
     * @param string $message
     * @return $this
     */
    public function addFlash($key, $message)
    {
        $_SESSION['__flashBag'][$key][] = $message;

        return $this;
    }

    /**
     * Returns all flash messages or one by key
     *
     * @param string $key
     * @return mixed
     */
    public function getFlash($key = null)
    {
        return ($key && isset($_SESSION['__flashBag'][$key])) ? $_SESSION['__flashBag'][$key] :
            (isset($_SESSION['__flashBag'])) ? $_SESSION['__flashBag'] : null;
    }

    /**
     * Removes all flash messages or one by key
     *
     * @param string $key
     * @return $this
     */
    public function cleanUpFlash($key = null)
    {
        if ($key && isset($_SESSION['__flashBag'][$key])) {
            unset($_SESSION['__flashBag'][$key]);
        } else {
            unset($_SESSION['__flashBag']);
        }

        return $this;
    }

    /**
     * Prints out all flash messages and deletes them
     *
     * @param string $markup message markup Default: <p class="%s">%s<p/> -> printf($markup, $key, $message)
     * @return $this
     */
    public function showFlash($markup = '<p class="%s">%s<p/>')
    {
        if (!empty($_SESSION['__flashBag'])) {
            foreach ($_SESSION['__flashBag'] as $key => $group) {
                foreach ($group as $message) {
                    printf($markup, $key, $message);
                }
            }
        }
        $this->cleanUpFlash();

        return $this;
    }

    /**
     * Generates an unique token and store it in the session
     */
    private function generateToken()
    {
        if ($this->get('__token') != null) {
            // Store old token in session
            $this->set('__previous_token', $this->get('__token'));
        }

        // Generate new token
        $this->set('__token', hash('sha256', uniqid(mt_rand(), true)));
    }

    /**
     * Returns the current token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->get('__token');
    }

    /**
     * Returns the previous token
     *
     * @return string
     */
    public function getPreviousToken()
    {
        return $this->get('__previous_token');
    }

}
