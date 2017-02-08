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
namespace Arhitector\Transcoder;

use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Service\ServiceFactoryInterface;
use Arhitector\Transcoder\Stream\StreamInterface;
use Arhitector\Transcoder\Traits\FilePathAwareTrait;
use Mimey\MimeTypes;

/**
 * Class TranscoderTrait.
 *
 * @package Arhitector\Transcoder
 */
trait TranscodeTrait
{
	use FilePathAwareTrait;
	
	/**
	 * @var FormatInterface
	 */
	protected $format;
	
	/**
	 * @var \Arhitector\Transcoder\Stream\Collection
	 */
	protected $streams;
	
	/**
	 * @var ServiceFactoryInterface Service factory instance.
	 */
	protected $service;
	
	/**
	 * @var string The MIME Content-type for a file.
	 */
	protected $mimeType;
	
	/**
	 * @var \Arhitector\Transcoder\Filter\Collection List of filters.
	 */
	protected $filters;
	
	/**
	 * Get current format.
	 *
	 * @return FormatInterface
	 */
	public function getFormat()
	{
		return $this->format;
	}
	
	/**
	 * Get a list of streams.
	 *
	 * @return \Arhitector\Transcoder\Stream\Collection|StreamInterface[]
	 */
	public function getStreams()
	{
		return $this->streams;
	}
	
	/**
	 * Get the service instance.
	 *
	 * @return ServiceFactoryInterface
	 */
	public function getService()
	{
		return $this->service;
	}
	
	/**
	 * Add a new stream.
	 *
	 * @param StreamInterface $stream
	 *
	 * @return TranscodeInterface
	 */
	public function addStream(StreamInterface $stream)
	{
		
	}
	
	/**
	 * Set the service instance.
	 *
	 * @param ServiceFactoryInterface $service
	 *
	 * @return TranscodeTrait
	 */
	protected function setService(ServiceFactoryInterface $service)
	{
		$this->service = $service;
		
		return $this;
	}
	
	/**
	 * Gets the MIME Content-type value.
	 *
	 * @return string
	 */
	protected function getMimeType()
	{
		if ( ! $this->mimeType)
		{
			$this->mimeType = mime_content_type($this->getFilePath());
		}
		
		return (string) $this->mimeType;
	}
	
	/**
	 * Find a format class.
	 *
	 * @param string $possibleFormat
	 * @param mixed  $default
	 *
	 * @return string|mixed
	 */
	protected function findFormatClass($possibleFormat = null, $default = null)
	{
		static $mimeTypes = null;
		
		if ($possibleFormat !== null)
		{
			$className = __NAMESPACE__.'\\Format\\'.ucfirst($possibleFormat);
			
			if (class_exists($className))
			{
				return $className;
			}
		}
		
		if ( ! $mimeTypes)
		{
			$mimeTypes = new MimeTypes();
		}
		
		$extension = pathinfo($this->getFilePath(), PATHINFO_EXTENSION);
		
		if ($extension)
		{
			$extensions = $mimeTypes->getAllExtensions($this->getMimeType());
			
			if ( ! in_array($extension, $extensions, false))
			{
				$extension = reset($extensions);
			}
			
			$classString = __NAMESPACE__.'\\Format\\'.ucfirst($extension);
			
			if (class_exists($classString))
			{
				return $classString;
			}
		}

		return $default;
	}
	
}
