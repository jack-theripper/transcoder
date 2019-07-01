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
 * The Jpeg picture format.
 *
 * @package Arhitector\Transcoder\Format
 */
class Jpeg extends FrameFormat
{
	
	/**
	 * Format constructor.
	 *
	 * @param Codec|string $codec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($codec = 'mjpeg')
	{
		parent::__construct($codec);
		
		$this->setExtensions(['jpg', 'jpeg']);
		$this->setAvailableVideoCodecs(['jpeg2000', 'libopenjpeg', 'jpegls', 'ljpeg', 'mjpeg']);
	}
	
}
