<?php
/**
 * This file is part of the arhitector/jumper library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Jumper;

use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\Service\ServiceFactoryInterface;
use Arhitector\Jumper\Stream\StreamInterface;

/**
 * Class TranscoderTrait.
 *
 * @package Arhitector\Jumper
 */
trait TranscoderTrait
{
	
	/**
	 * @var FormatInterface
	 */
	protected $format;
	
	/**
	 * @var \Arhitector\Jumper\Stream\Collection
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
	 * @return \Arhitector\Jumper\Stream\Collection|StreamInterface[]
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
	 * Set the service instance.
	 *
	 * @param ServiceFactoryInterface $service
	 *
	 * @return TranscoderTrait
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
	
}
