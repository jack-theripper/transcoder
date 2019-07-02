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

use Psr\Http\Message\StreamInterface;

/**
 * A wrapper that allows you to use psr7-stream as a source.
 *
 * @package Arhitector\Transcoder\Protocol
 * @todo    Write to psr7-stream if used as output.
 */
class Psr7Stream extends FileSystem implements ProtocolInterface
{
	
	/**
	 * Read up to READ_LENGTH bytes.
	 */
	const READ_LENGTH = 1048576;
	
	/**
	 * @var \Psr\Http\Message\StreamInterface
	 */
	protected $stream;
	
	/**
	 * Psr7Stream constructor.
	 *
	 * @noinspection PhpMissingParentConstructorInspection
	 *
	 * @param \Psr\Http\Message\StreamInterface $stream
	 */
	public function __construct(StreamInterface $stream)
	{
		$this->stream = $stream;
		$this->setFilePath($this->create());
		
		register_shutdown_function([$this, '__destruct']);
	}
	
	/**
	 * @return StreamInterface
	 */
	public function getStream(): StreamInterface
	{
		return $this->stream;
	}
	
	/**
	 * Returns a specific Protocol option as a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		if ( ! $this->getStream()->eof())
		{
			while ( ! $this->getStream()->eof())
			{
				file_put_contents($this->getFilePath(), $this->getStream()->read(self::READ_LENGTH), FILE_APPEND);
			}
		}
		
		return parent::__toString();
	}
	
	/**
	 * Destruct every temporary file.
	 */
	public function __destruct()
	{
		if ( ! ($filePath = $this->getFilePath()) || ! file_exists($filePath))
		{
			return;
		}
		
		unlink($filePath);
		clearstatcache(false, $filePath); // Wiping out changes in local file cache
	}
	
	/**
	 * Create file with unique name in temp directory.
	 *
	 * @return string
	 * @throws \Error
	 */
	private function create()
	{
		if ( ! ($filename = tempnam(sys_get_temp_dir(), 'transcoder')))
		{
			throw new \Error('The function tempnam() could not create a file in temporary directory.');
		}
		
		return $filename;
	}
	
}
