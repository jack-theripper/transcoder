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
namespace Arhitector\Transcoder\Format;

use Arhitector\Transcoder\Codec;

/**
 * The Mkv video format.
 *
 * @package Arhitector\Transcoder\Format
 */
class Mkv extends VideoFormat
{
	
	/**
	 * Format constructor.
	 *
	 * @param Codec|string $audioCodec
	 * @param Codec|string $videoCodec
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($audioCodec = 'mp3', $videoCodec = 'libtheora')
	{
		parent::__construct($audioCodec, $videoCodec);
		
		$this->setExtensions(['mkv']);
		$this->setAvailableVideoCodecs(['mpeg4', 'mpeg1', 'mpeg2', 'theora', 'libtheora', 'mpeg1video', 'mpeg2video']);
		$this->setAvailableAudioCodecs([
			'ac3',
			'mp1',
			'mp2',
			'mp3',
			'dts',
			'tta',
			'libvorbis',
			'vorbis',
			'flac',
			'ra_144',
			'libfdk_aac',
			'libfaac',
			'aac',
			'libvo_aacenc',
			'pcm_alaw',
			'pcm_f32le',
			'pcm_f64le',
			'pcm_lxf',
			'pcm_mulaw',
			'pcm_s16le',
			'pcm_s16le_planar',
			'pcm_s24daud',
			'pcm_s24le',
			'pcm_s24le_planar',
			'pcm_s32le',
			'pcm_s32le_planar',
			'pcm_s8',
			'pcm_s8_planar',
			'pcm_u16le',
			'pcm_u24le',
			'pcm_u32le',
			'pcm_u8'
		]);
	}
	
}
