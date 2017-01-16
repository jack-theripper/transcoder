<?php
/**
 * This file is part of the arhitector/jumper library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 *
 * @license   http://opensource.org/licenses/MIT MIT
 * @copyright Copyright (c) 2017 Dmitry Arhitector <dmitry.arhitector@yandex.ru>
 */
namespace Arhitector\Jumper\Service;

use Arhitector\Jumper\Exception\ExecutableNotFoundException;
use Arhitector\Jumper\Format\AudioFormatInterface;
use Arhitector\Jumper\Format\FormatInterface;
use Arhitector\Jumper\TranscoderInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class Encoder.
 *
 * @package Arhitector\Jumper\Service
 */
class Encoder
{
	
	/**
	 * @var array The options.
	 */
	protected $options;
	
	/**
	 * Encoder constructor.
	 *
	 * @param array $options
	 *
	 * @throws ExecutableNotFoundException
	 */
	public function __construct(array $options = [])
	{
		$this->setOptions($this->resolveOptions($options));
	}
	
	/**
	 * Constructs and returns the iterator with instances of 'Process'.
	 * If the value $media instance of 'TranscoderInterface' then it is full media or instance of 'StreamInterface'
	 * then it is stream.
	 *
	 * @param TranscoderInterface $media  it may be a stream or media wrapper.
	 * @param FormatInterface     $format new format.
	 * @param array               $options
	 *
	 * @return \Iterator|\Symfony\Component\Process\Process[] returns the instances of 'Process'.
	 * @throws \RuntimeException
	 */
	public function transcoding(TranscoderInterface $media, FormatInterface $format, array $options = [])
	{
		$ffmpegOptions = array_merge([
			'y'      => '',
			'i'      => [$media->getFilePath()],
			'strict' => '-2'
		], $this->getFormatOptions($format), $this->getForceFormatOptions($format));
		
		if ( ! isset($options['output']))
		{
			throw new \RuntimeException('Output file path not found.');
		}
		
		$filePath = $options['output'];
		
		$options = array_diff_key($ffmpegOptions, array_fill_keys([
			'disable_audio',
			'audio_quality',
			'audio_codec',
			'audio_bitrate',
			'audio_sample_frequency',
			'audio_channels',
			'video_codec',
			'force_format',
			'output',
			
			// опции адаптера
			'ffmpeg_force_format',
		], null));
		
		foreach ([
			         'disable_audio'          => '-an',
			         'audio_quality'          => '-qscale:a',
			         'audio_codec'            => '-codec:a',
			         'audio_bitrate'          => '-b:a',
			         'audio_sample_frequency' => '-ar',
			         'audio_channels'         => '-ac',
			         'video_codec'            => '-codec:v',
			         'ffmpeg_force_format'    => '-f'
		         ] as $option => $value)
		{
			if (isset($ffmpegOptions[$option]))
			{
				$options[$value] = $ffmpegOptions[$option];
				
				if (is_bool($ffmpegOptions[$option]))
				{
					$options[$value] = '';
				}
			}
		}
		
		$ffmpegOptions = [];
		
		foreach ($options as $option => $value)
		{
			$ffmpegOptions[] = '-'.ltrim($option, '-');
			
			if (is_array($value))
			{
				array_pop($ffmpegOptions);
				
				foreach ($value as $key => $val)
				{
					$ffmpegOptions[] = '-'.ltrim($option, '-');
					$ffmpegOptions[] = is_int($key) ? $val : "{$key}={$val}";
				}
				
			}
			else if ($value)
			{
				$ffmpegOptions[] = $value;
			}
		}
		
		$ffmpegOptions[] = $filePath;
		
		yield (new ProcessBuilder($ffmpegOptions))->setPrefix($this->options['ffmpeg.path'])->getProcess();
	}
	
	/**
	 * Sets the options value.
	 *
	 * @param array $options
	 *
	 * @return Encoder
	 */
	protected function setOptions(array $options)
	{
		$this->options = $options;
		
		return $this;
	}
	
	/**
	 * The options processing.
	 *
	 * @param array $options
	 *
	 * @return array
	 * @throws \Arhitector\Jumper\Exception\ExecutableNotFoundException
	 */
	protected function resolveOptions(array $options)
	{
		return array_merge([
			'ffmpeg.path'    => 'ffmpeg',
			'ffmpeg.threads' => 0,
			'timeout'        => 60
		], $options);
	}
	
	/**
	 * Get force format options.
	 *
	 * @param $format
	 *
	 * @return array
	 */
	protected function getForceFormatOptions(FormatInterface $format)
	{
		return [];
	}
	
	/**
	 * Get options for format.
	 *
	 * @param FormatInterface $format
	 *
	 * @return array
	 */
	protected function getFormatOptions(FormatInterface $format)
	{
		$options = [];
		
		if ($format instanceof AudioFormatInterface)
		{
			$options['audio_codec'] = (string) $format->getAudioCodec() ?: 'copy';
			$options['video_codec'] = 'copy';
			
			if ($format->getAudioBitrate() > 0)
			{
				$options['audio_bitrate'] = $format->getAudioBitrate();
			}
			
			if ($format->getAudioFrequency() > 0)
			{
				$options['audio_sample_frequency'] = $format->getAudioFrequency();
			}
			
			if ($format->getAudioChannels() > 0)
			{
				$options['audio_channels'] = $format->getAudioChannels();
			}
		}
		
		return $options;
	}
	
}