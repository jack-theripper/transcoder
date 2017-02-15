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

use Arhitector\Transcoder\Exception\InvalidFilterException;
use Arhitector\Transcoder\Exception\TranscoderException;
use Arhitector\Transcoder\Filter\AudioFilterInterface;
use Arhitector\Transcoder\Filter\FilterInterface;
use Arhitector\Transcoder\Filter\FrameFilterInterface;
use Arhitector\Transcoder\Format\VideoFormat;
use Arhitector\Transcoder\Format\VideoFormatInterface;
use Arhitector\Transcoder\Service\ServiceFactoryInterface;
use Arhitector\Transcoder\Stream\AudioStream;
use Arhitector\Transcoder\Stream\Collection;
use Arhitector\Transcoder\Stream\VideoStream;

/**
 * Class Video.
 *
 * @package Arhitector\Transcoder
 */
class Video extends Audio implements VideoInterface
{
	
	/**
	 * Video constructor.
	 *
	 * @param string                  $filePath
	 * @param ServiceFactoryInterface $service
	 *
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function __construct($filePath, ServiceFactoryInterface $service = null)
	{
		parent::__construct($filePath, $service);
	}
	
	/**
	 * Get current format.
	 *
	 * @return VideoFormatInterface|\Arhitector\Transcoder\Format\FormatInterface
	 */
	public function getFormat()
	{
		return parent::getFormat();
	}
	
	/**
	 * Returns the video codec.
	 *
	 * @return Codec|null
	 */
	public function getVideoCodec()
	{
		return $this->getFormat()->getFrameCodec();
	}
	
	/**
	 * Get width value.
	 *
	 * @return int
	 */
	public function getWidth()
	{
		return $this->getFormat()->getWidth();
	}
	
	/**
	 * Get height value.
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->getFormat()->getHeight();
	}
	
	/**
	 * Gets the bitrate value.
	 *
	 * @return int
	 */
	public function getKiloBitrate()
	{
		return (int) ($this->getFormat()->getVideoBitrate() / 1000);
	}
	
	/**
	 * Get frame rate value.
	 *
	 * @return float
	 */
	public function getFrameRate()
	{
		return $this->getFormat()->getFrameRate();
	}
	
	/**
	 * Add a new filter.
	 *
	 * @param FilterInterface $filter
	 * @param int             $priority range 0-99.
	 *
	 * @return TranscodeInterface
	 * @throws \InvalidArgumentException
	 * @throws \RangeException
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0)
	{
		if ( ! $filter instanceof FrameFilterInterface && ! $filter instanceof AudioFilterInterface)
		{
			throw new InvalidFilterException('Filter type is not supported.');
		}
		
		if ($priority > 99)
		{
			throw new \RangeException('Priority should be in the range from 0 to 99.');
		}
		
		$this->filters->insert($filter, $priority);
		
		return $this;
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		return ! (stripos($this->getMimeType(), 'video/') !== 0);
	}
	
	/**
	 * Ensure streams etc.
	 *
	 * @param \stdClass $demuxing
	 *
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	protected function _createCollections($demuxing)
	{
		$this->streams = new Collection(array_map(function ($parameters) {
			if ($parameters['type'] == 'audio')
			{
				return AudioStream::create($this, $parameters);
			}
			
			if ($parameters['type'] == 'video')
			{
				return VideoStream::create($this, $parameters);
			}
			
			throw new TranscoderException('This stream unsupported.');
		}, $demuxing->streams));
		
		/** @var VideoFormatInterface $className */
		$className = $this->findFormatClass($demuxing->format['format'], VideoFormat::class);
		
		if ( ! $className instanceof VideoFormatInterface)
		{
			$className = VideoFormat::class;
		}
		
		$demuxing->format += $this->getStreams()[0]->toArray();
		$this->format = $className::fromArray(array_filter($demuxing->format, function ($value) {
			return $value !== null;
		}));
		
	}
	
}
