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
namespace Arhitector\Transcoder\Format;

use Arhitector\Transcoder\Codec;

/**
 * Class SubtitleFormat.
 *
 * @package Arhitector\Transcoder\Format
 */
class SubtitleFormat implements SubtitleFormatInterface
{
	use FormatTrait;
	
	/**
	 * @var Codec Subtitle codec value.
	 */
	protected $codec;
	
	/**
	 * SubtitleFormat constructor.
	 *
	 * @param Codec|string $codec
	 */
	public function __construct($codec = null)
	{
		if ($codec !== null)
		{
			if ( ! $codec instanceof Codec)
			{
				$codec = new Codec($codec, '');
			}
			
			$this->codec = $codec;
		}
	}
	
	/**
	 * Returns the number of passes.
	 *
	 * @return int
	 */
	public function getPasses()
	{
		return 1;
	}
	
}
