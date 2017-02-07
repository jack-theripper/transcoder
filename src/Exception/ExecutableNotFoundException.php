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

use Exception;

/**
 * Executable file not found.
 *
 * @package Arhitector\Transcoder\Exception
 */
class ExecutableNotFoundException extends TranscoderException
{
	
	/**
	 * @var string Executable file.
	 */
	protected $executable;
	
	/**
	 * Construct the exception.
	 *
	 * @param string    $message  The Exception message to throw.
	 * @param string    $executableFile
	 * @param Exception $previous The previous exception used for the exception chaining.
	 */
	public function __construct($message, $executableFile, Exception $previous = null)
	{
		$this->setExecutableFile($executableFile);
		
		parent::__construct($message, 0, $previous);
	}
	
	/**
	 * Get binary value.
	 *
	 * @return string
	 */
	public function getExecutableFile()
	{
		return $this->executable;
	}
	
	/**
	 * Sets the binary value.
	 *
	 * @param string $executableFile
	 *
	 * @return ExecutableNotFoundException
	 */
	protected function setExecutableFile($executableFile)
	{
		$this->executable = (string) $executableFile;
		
		return $this;
	}
	
}
