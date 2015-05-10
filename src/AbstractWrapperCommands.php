<?php namespace Williamson\Laragram;

use InvalidArgumentException;
use Socket\Raw\Factory;

abstract class AbstractWrapperCommands
{

    /**
     * The file handler for the socket-connection
     *
     * @var ressource
     */
    protected $_fp;
    protected $socket;

    /**
     * Connects to the telegram-cli.
     *
     * @param string $remoteSocket Address of the socket to connect to. See stream_socket_client() for more info.
     *                             Can be 'unix://' or 'tcp://'.
     *
     * @throws ClientException Throws an exception if no connection can be established.
     */
    public function __construct($remoteSocket)
    {
        $factory = new Factory();
        $this->socket = $factory->createClient($remoteSocket);
//        if ($this->_fp === false) {
//            throw new ClientException('Could not connect to socket "' . $remoteSocket . '"');
//        }
//        stream_set_timeout($this->_fp, 1); //This way fgets() returns false if telegram-cli gives us no response.
    }

    /**
     * Closes the connection to the telegram-cli.
     */
    public function __destruct()
    {

    }

    /**
     * Executes a command on the telegram-cli. Line-breaks will be escaped, as telgram-cli does not support them.
     *
     * @param string $command The command, including all arguments
     *
     * @return boolean|string Returns the answer as string or true on success, false if there was an error.
     */
    public function exec($command)
    {
        $this->socket->write(str_replace("\n", '\n', $command) . PHP_EOL);

        $answer = $this->socket->read(4096, PHP_NORMAL_READ); //"ANSWER $bytes" if there is a return value or \n if not
        if (is_string($answer)) {
            if (substr($answer, 0, 7) === 'ANSWER ') {
                $bytes = (int) substr($answer, 7);
                if ($bytes > 0) {
                    $string = trim($this->socket->read($bytes + 1));

                    if ($string === 'SUCCESS') { //For "status_online" and "status_offline"
                        return true;
                    }

                    return $string;
                }
            } else {
                if ($answer === PHP_EOL) { //For commands like "msg"
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Escapes strings for usage as command-argument.
     * T"es't -> "T\"es\'t"
     *
     * @param string $argument The argument to escape
     *
     * @return string The escaped command enclosed by double-quotes
     */
    public function escapeStringArgument($argument)
    {
        return '"' . addslashes($argument) . '"';
    }

    /**
     * Replaces all spaces with underscores.
     *
     * @param string $peer The peer to escape
     *
     * @return string The escaped peer
     */
    public function escapePeer($peer)
    {
        return str_replace(' ', '_', $peer);
    }

}
