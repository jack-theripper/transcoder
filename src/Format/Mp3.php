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
 * Class Mp3.
 *
 * @package Arhitector\Transcoder\Format
 */
class Mp3 extends AudioFormat
{
	
	/**
	 * AudioFormat constructor.
	 *
	 * @param Codec|string $audioCodec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($audioCodec = 'libmp3lame')
	{
		$this->setExtensions(['mp2', 'mp3', 'm2a']);
		$this->setAvailableAudioCodecs(['libmp3lame', 'libshine', 'mp3', 'mp3pro', 'lame']);
		
		parent::__construct($audioCodec);
	}
	
}
