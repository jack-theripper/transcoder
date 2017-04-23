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
namespace Arhitector\Transcoder\Filter;

use Arhitector\Transcoder\Dimension;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\FrameFormatInterface;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Class Resize.
 *
 * @package Arhitector\Transcoder\Filter
 */
class Resize implements FrameFilterInterface
{
	
	/**
	 * @var int The width value.
	 */
	protected $width;
	
	/**
	 * @var int The height value.
	 */
	protected $height;
	
	/**
	 * Resize constructor.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($width = null, $height = null)
	{
		if ( ! is_numeric($width) && ! is_numeric($height))
		{
			throw new \InvalidArgumentException('Wrong parameters scaling.');
		}
		
		$this->width = (int) $width;
		$this->height = (int) $height;
	}
	
	/**
	 * Apply filter.
	 *
	 * @param TranscodeInterface $media
	 * @param FormatInterface    $format
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function apply(TranscodeInterface $media, FormatInterface $format)
	{
		if ( ! $format instanceof FrameFormatInterface)
		{
			throw new \InvalidArgumentException('The filter resize can be used only with the format of the frame.');
		}
		
		$dimension = new Dimension($format->getWidth(), $format->getHeight());
		
		if ($this->width && ! $this->height) // Resize to width
		{
			$this->height = $this->width / $dimension->getRatio();
		}
		
		if ( ! $this->width && $this->height) // Resize to height
		{
			$this->width = $this->height * $dimension->getRatio();
		}
		
		// If the dimensions are the same, there's no need to resize.
		if($dimension->getWidth() === $this->width && $dimension->getHeight() === $this->height)
		{
			return [];
		}
		
		return [
			'filter:v' => [
				'scale' => http_build_query([
					'w' => $this->width,
					'h' => $this->height
				], null, ':')
			]
		];
	}
	
}
