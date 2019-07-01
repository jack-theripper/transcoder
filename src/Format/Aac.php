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
 * The Aac audio format.
 *
 * @package Arhitector\Transcoder\Format
 */
class Aac extends AudioFormat
{
	
	/**
	 * Format constructor.
	 *
	 * @param Codec|string $audioCodec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($audioCodec = 'aac')
	{
		parent::__construct($audioCodec);
		
		$this->setExtensions(['aac']);
		$this->setAvailableAudioCodecs(['libfdk_aac', 'libfaac', 'aac', 'libvo_aacenc', 'faac']);
	}
	
}
