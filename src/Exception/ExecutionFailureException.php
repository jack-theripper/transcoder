<?php
/**
 * This file is part of the arhitector/transcoder library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Exception;

use Symfony\Component\Process\Process;
use Exception;

/**
 * Class ExecutionFailureException.
 *
 * @package Arhitector\Transcoder\Exception
 */
class ExecutionFailureException extends TranscoderException
{
	
	/**
	 * @var Process  Process instance.
	 */
	protected $process;
	
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 *
	 * @param string    $message  The Exception message to throw.
	 * @param Process   $process  Process instance.
	 * @param int       $code     The Exception code.
	 * @param Exception $previous The previous exception used for the exception chaining. Since 5.3.0
	 *
	 * @throws \Symfony\Component\Process\Exception\RuntimeException
	 */
	public function __construct($message, Process $process, $code = null, Exception $previous = null)
	{
		parent::__construct($message, (int) $process->getExitCode(), $previous);
		
		$this->setProcess($process);
	}
	
	/**
	 * Wrapper for the command line.
	 *
	 * @return string
	 */
	public function getCommandLine()
	{
		return $this->getProcess()
			->getCommandLine();
	}
	
	/**
	 * Get current process.
	 *
	 * @return Process
	 */
	public function getProcess()
	{
		return $this->process;
	}
	
	/**
	 * Set process instance.
	 *
	 * @param Process $process
	 *
	 * @return ExecutionFailureException
	 */
	protected function setProcess(Process $process)
	{
		$this->process = $process;
		
		return $this;
	}
	
}
