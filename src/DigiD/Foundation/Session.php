<?php

namespace Yard\DigiD\Foundation;

class Session
{
    const SESSION_STARTED = true;
    const SESSION_NOT_STARTED = false;

    /**
     * The state of the session.
     *
     * @var bool
     */
    private $sessionState = self::SESSION_NOT_STARTED;

    /**
     * @var Session
     */
    private static $instance;

    final private function __construct()
    {
    }

    /**
    *    Returns the instance of 'Session'.
    *    The session is automatically initialized if it wasn't.
    *
    *    @return    self
    **/
    public static function getInstance(): self
    {
        if (null == static::$instance) {
            self::$instance = new static();
        }

        self::$instance->startSession();

        return self::$instance;
    }

    /**
    * Starts the session.
    *
    * @return bool TRUE if the session has been initialized, else FALSE.
    **/
    public function startSession(): bool
    {
        if (self::SESSION_NOT_STARTED == $this->sessionState) {
            $this->sessionState = session_start();
        }
        return $this->sessionState;
    }

    /**
    *    Stores data in session.
    *    Example: $instance->set('foo', 'bar');
    *
    *    @param    string $name    Name of the data.
    *    @param    string $value
    *    @param    string $namespace
    *    @return   void
    **/
    public function set(string $name, ?string $value, string $namespace = ''): void
    {
        if ($namespace) {
            $_SESSION[$namespace][$name] = $value;
        } else {
            $_SESSION[$name] = $value;
        }
    }

    /**
    *    Get data from session.
    *    Example: echo $instance->get('foo');
    *
    *    @param    string $value  Name of the datas to get.
    *    @param    string $namespace
    *    @return   mixed    Data stored in session.
    **/
    public function get($value, $namespace = '')
    {
        if ($namespace) {
            return $_SESSION[$namespace][$value] ?? null;
        } else {
            return $_SESSION[$value] ?? null;
        }
    }

    /**
     * Check if value exists.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function __isset($name): bool
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Remove value from session
     *
     * @param string $name
     * @return void
     */
    public function __unset($name): void
    {
        unset($_SESSION[$name]);
    }

    /**
    * Destroys the current session.
    *
    * @return    bool    TRUE is session has been deleted, else FALSE.
    **/
    public function destroy(): bool
    {
        if (self::SESSION_STARTED == $this->sessionState) {
            $this->sessionState = !session_destroy();
            unset($_SESSION);

            return !$this->sessionState;
        }

        return false;
    }
}
