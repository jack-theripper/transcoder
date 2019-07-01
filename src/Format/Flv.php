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
 * The Flv video format.
 *
 * @package Arhitector\Transcoder\Format
 */
class Flv extends VideoFormat
{
	
	/**
	 * VideoFormat constructor.
	 *
	 * @param Codec|string $audioCodec
	 * @param Codec|string $videoCodec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($audioCodec = 'libmp3lame', $videoCodec = 'flv1')
	{
		parent::__construct($audioCodec, $videoCodec);
		
		$this->setExtensions(['flv']);
		$this->setAvailableVideoCodecs(['flv', 'flv1']);
		$this->setAvailableAudioCodecs(['libmp3lame', 'libshine', 'mp3', 'mp3pro', 'lame']);
	}
	
	/**
	 * Set frequency value.
	 *
	 * @param int $frequency
	 *
	 * @return Flv
	 * @throws \InvalidArgumentException
	 */
	public function setFrequency($frequency)
	{
		$frequencies = [44100, 22050, 11025];
		
		if ( ! in_array($frequency, $frequencies, false))
		{
			throw new \InvalidArgumentException(sprintf('Wrong sample rate value for %s, available values are %s',
				$frequency, implode(', ', $frequencies)));
		}
		
		parent::setFrequency($frequency);
		
		return $this;
	}
	
}
