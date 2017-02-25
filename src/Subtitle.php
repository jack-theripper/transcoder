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
use Arhitector\Transcoder\Format\SubtitleFormat;
use Arhitector\Transcoder\Format\SubtitleFormatInterface;

/**
 * Class Subtitle.
 *
 * @package Arhitector\Transcoder
 */
class Subtitle implements SubtitleInterface
{
	use TranscodeTrait;
	
	/**
	 * Get duration value.
	 *
	 * @return float
	 */
	public function getDuration()
	{
		// TODO: Implement getDuration() method.
	}
	
	/**
	 * Transcoding.
	 *
	 * @param FormatInterface $format
	 * @param string          $filePath
	 * @param bool            $overwrite
	 *
	 * @return float|int Size of the new file or -1
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
		$format = $this->findFormatClass($formatArray['format'], SubtitleFormat::class);
		
		if ( ! is_subclass_of($format, SubtitleFormatInterface::class))
		{
			throw new TranscoderException('Invalid format type.');
		}
		
		return $format::fromArray(array_filter($formatArray, function ($value) {
			return $value !== null;
		}));
	}
	
	/**
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		return ! (stripos($this->getMimeType(), 'text/plain') !== 0);
	}
	
}
