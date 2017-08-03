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

use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\FrameFormatInterface;
use Arhitector\Transcoder\Traits\ConvertEncodingTrait;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Draw a text string or text from a specified file on top of a video.
 *
 * @package Arhitector\Transcoder\Filter
 */
class Text implements FrameFilterInterface
{
	use ConvertEncodingTrait;
	
	/**
	 * @var int The font size to be used for drawing text.
	 */
	protected $size = 16;
	
	/**
	 * @var string The text string to be drawn.
	 */
	protected $content;
	
	/**
	 * @var string The color to be used for drawing fonts.
	 */
	protected $color = 'black';

	/**
	 * Text constructor.
	 *
	 * @param string $content
	 */
	public function __construct($content = null)
	{
		if ($content !== null)
		{
			$this->setContent($content);
		}
	}
	
	/**
	 * Returns the font size.
	 *
	 * @return int
	 */
	public function getSize()
	{
		return $this->size;
	}
	
	/**
	 * Sets the font size to be used for drawing text.
	 *
	 * @param int $size
	 *
	 * @return Text
	 */
	public function setSize($size)
	{
		$this->size = (int) $size;
		
		return $this;
	}
	
	/**
	 * Returns the text string.
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}
	
	/**
	 * Sets the text string to be drawn.
	 *
	 * @param string $content
	 *
	 * @return Text
	 */
	public function setContent($content)
	{
		$this->content = $content;
		
		return $this;
	}
	
	/**
	 * Sets the color to be used for drawing fonts.
	 *
	 * @param string $color
	 *
	 * @return Text
	 */
	public function setColor($color)
	{
		$this->color = (string) $color;
		
		return $this;
	}
	
	/**
	 * Get the color value.
	 *
	 * @return string
	 */
	public function getColor()
	{
		return $this->color;
	}
	
	/**
	 * Apply filter.
	 *
	 * @param TranscodeInterface $media
	 * @param FormatInterface    $format
	 *
	 * @return array
	 */
	public function apply(TranscodeInterface $media, FormatInterface $format)
	{
		if ( ! $format instanceof FrameFormatInterface)
		{
			throw new \InvalidArgumentException('The filter text can be used only with the format of the frame.');
		}
		
		$text = "text='{$this->convertEncoding($this->getContent())}':";
		
		return [
			'filter:v' => [
				'drawtext' => $text.http_build_query([
						'fontsize'  => $this->getSize(),
						'fontcolor' => $this->getColor(),
						'x'         => 0,
						'y'         => 0,
					], null, ':')
			]
		];
	}
	
}