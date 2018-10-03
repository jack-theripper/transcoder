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
 * @copyright Copyright (c) 2017-2018 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Protocol;

use Arhitector\Transcoder\Exception\TranscoderException;
use InvalidArgumentException;

/**
 * Class FileSystem
 *
 * @package Arhitector\Transcoder\Protocol
 */
class FileSystem implements ProtocolInterface
{
	/**
	 * @var string  The full path to the file.
	 */
	protected $filePath;
	
	/**
	 * FileSystem constructor.
	 *
	 * @param string $filePath
	 */
	public function __construct($filePath)
	{
		$this->setFilePath($filePath);
	}
	
	/**
	 * Get the full path to the file.
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}
	
	/**
	 * Set file path.
	 *
	 * @param string $filePath
	 *
	 * @return FileSystem
	 * @throws InvalidArgumentException
	 * @throws TranscoderException
	 */
	protected function setFilePath($filePath)
	{
		if ( ! is_string($filePath))
		{
			throw new InvalidArgumentException('File path must be a string type.');
		}
		
		if (preg_match('~^(\w+:)?//~', $filePath) || is_link($filePath))
		{
			throw new InvalidArgumentException('File path must be a local path.');
		}
		
		$filePath = realpath($filePath);
		
		if ( ! is_file($filePath))
		{
			throw new TranscoderException('File path not found.');
		}
		
		$this->filePath = $filePath;
		
		return $this;
	}
	
}