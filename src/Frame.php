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
use Arhitector\Transcoder\Filter\FilterInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\FrameFormat;
use Arhitector\Transcoder\Format\FrameFormatInterface;
use Arhitector\Transcoder\Stream\StreamInterface;

/**
 * Class Frame.
 *
 * @package Arhitector\Transcoder
 */
class Frame implements FrameInterface
{
	use TranscodeTrait {
		TranscodeTrait::getFormat as private _getFormat;
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
	 * Get current format.
	 *
	 * @return FrameFormatInterface|\Arhitector\Transcoder\Format\FormatInterface
	 */
	public function getFormat()
	{
		return $this->_getFormat();
	}
	
	/**
	 * Get duration value.
	 *
	 * @return float
	 */
	public function getDuration()
	{
		return 0.0;
	}
	
	/**
	 * Transcoding.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return TranscodeInterface
	 */
	public function save(FormatInterface $format, $filePath, $overwrite = true)
	{
		// TODO: Implement save() method.
	}
	
	/**
	 * Add a new filter.
	 *
	 * @param FilterInterface $filter
	 * @param int             $priority range 0-99.
	 *
	 * @return TranscodeInterface
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0)
	{
		// TODO: Implement addFilter() method.
	}
	
	/**
	 * Reset filters.
	 *
	 * @return TranscodeInterface
	 */
	public function withoutFilters()
	{
		// TODO: Implement withoutFilters() method.
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		return ! (stripos($this->getMimeType(), 'image/') !== 0);
	}
	
	/**
	 * Creates an instance of the format from the internal type.
	 *
	 * @param array $formatArray
	 *
	 * @return FormatInterface
	 * @throws \InvalidArgumentException
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 */
	protected function createFormat(array $formatArray)
	{
		$format = $this->findFormatClass($formatArray['format'], FrameFormat::class);
		
		if ( ! is_subclass_of($format, FrameFormatInterface::class))
		{
			throw new TranscoderException('Invalid format type.');
		}
		
		if ($stream = $this->getStreams(StreamInterface::T_FRAME)->getFirst())
		{
			foreach ($stream->toArray() as $key => $value)
			{
				$formatArray[$key] = $value;
				
				if ($key == 'codec')
				{
					$formatArray['video_codec'] = $value;
				}
			}
		}
		
		return $format::fromArray(array_filter($formatArray, function ($value) {
			return $value !== null;
		}));
	}
	
}
