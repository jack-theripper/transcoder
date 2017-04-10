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
use Arhitector\Transcoder\Stream\VideoStreamInterface;

/**
 * Class Video.
 *
 * @package Arhitector\Transcoder
 */
class Video extends Audio implements VideoInterface
{
	
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
		return $this->getFormat()->getVideoCodec();
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
	 * Creates an instance of the format from the internal type.
	 *
	 * @param array $formatArray
	 *
	 * @return VideoFormatInterface
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 */
	protected function createFormat(array $formatArray)
	{
		$format = $this->findFormatClass($formatArray['format'], VideoFormat::class);
		
		if ( ! is_subclass_of($format, VideoFormatInterface::class))
		{
			throw new TranscoderException('Invalid format type.');
		}
		
		foreach ($this->getStreams(self::STREAM_AUDIO | self::STREAM_VIDEO) as $stream)
		{
			$prefix = $stream instanceof VideoStreamInterface ? 'video_' : 'audio_';
			
			foreach ($stream->toArray() as $key => $value)
			{
				if ($prefix == 'audio_')
				{
					$formatArray[$key] = $value;
					
					if (in_array($key, ['codec', 'bitrate'], false))
					{
						$formatArray[$prefix.$key] = $value;
					}
				}
				else if ($prefix == 'video_')
				{
					$formatArray[$key] = $value;
					
					if ($key == 'codec')
					{
						$formatArray['video_codec'] = $value;
					}
					
					if (in_array($key, ['bitrate'], false))
					{
						$formatArray[$prefix.$key] = $value;
					}
				}
			}
		}
		
		return $format::fromArray(array_filter($formatArray, function ($value) {
			return $value !== null;
		}));
	}
	
}
