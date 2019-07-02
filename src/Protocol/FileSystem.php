<?php
/**
 * This file is part of the arhitector/transcoder-ffmpeg library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017-2019 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Transcoder\Protocol;

use Arhitector\Transcoder\Exception\TranscoderException;
use InvalidArgumentException;
use Symfony\Component\Mime\MimeTypes;

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
	public function __construct(string $filePath)
	{
		$this->setFilePath($filePath);
	}
	
	/**
	 * Get the full path to the file.
	 *
	 * @return string
	 */
	public function getFilePath(): string
	{
		return $this->filePath;
	}
	
	/**
	 * Set the file path.
	 *
	 * @param string $filePath
	 *
	 * @return FileSystem
	 * @throws InvalidArgumentException
	 */
	protected function setFilePath(string $filePath): self
	{
		if (preg_match('~^(\w+:)?//~', $filePath) || is_link($filePath))
		{
			throw new InvalidArgumentException('The file path must be a local path.');
		}
		
		$this->filePath = $filePath;
		
		return $this;
	}
	
	/**
	 * Retrieve an external iterator
	 *
	 * @return \Traversable An instance of an object implementing Iterator or Traversable
	 */
	public function getIterator()
	{
		$filePath = realpath($this->getFilePath());
		
		if ( ! is_file($filePath)) // @todo check for readability?
		{
			throw new TranscoderException('The file path not found.');
		}
		
		$handle = fopen($filePath, 'rb');
		
		while ( ! feof($handle))
		{
			yield fread($handle, 2097152);
		}
		
		fclose($handle);
		
		return;
	}
	
	/**
	 * Return the mime type or `NULL`.
	 *
	 * @return string|null
	 */
	public function getMimeType(): ?string
	{
		return MimeTypes::getDefault()->guessMimeType($this->getFilePath());
	}
	
	/**
	 * Returns a specific Protocol option as a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return sprintf('file:%s', $this->getFilePath());
	}
	

	
}
