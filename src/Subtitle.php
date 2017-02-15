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
use Arhitector\Transcoder\Service\ServiceFactory;
use Arhitector\Transcoder\Service\ServiceFactoryInterface;
use Arhitector\Transcoder\Filter\Graph;

/**
 * Class Subtitle.
 *
 * @package Arhitector\Transcoder
 */
class Subtitle implements SubtitleInterface
{
	use TranscodeTrait;
	
	/**
	 * Subtitle constructor.
	 *
	 * @param string                  $filePath
	 * @param ServiceFactoryInterface $service
	 *
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 * @throws \InvalidArgumentException
	 */
	public function __construct($filePath, ServiceFactoryInterface $service = null)
	{
		$this->setFilePath($filePath);
		$this->setService($service ?: new ServiceFactory());
		
		/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
		$demuxing = $this->getService()->getDecoderService()->demuxing($this);
		
		if (count($demuxing->streams) < 1 || ( ! $this->isSupportedFileType() && empty($demuxing->format['format'])))
		{
			throw new TranscoderException('File type unsupported or the file is corrupted.');
		}
		
		$this->_createCollections($demuxing);
		$this->filters = new Graph();
	}
	
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
	 * It supports the type of media.
	 *
	 * @return bool
	 */
	protected function isSupportedFileType()
	{
		if (stripos($this->getMimeType(), 'text/plain') !== 0)
		{
			return false;
		}
		
		return true;
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
		// TODO
	}
	
}
