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
namespace Arhitector\Transcoder\Service;

use Arhitector\Transcoder\Exception\ExecutableNotFoundException;
use Arhitector\Transcoder\Format\AudioFormatInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\FrameFormatInterface;
use Arhitector\Transcoder\Format\VideoFormatInterface;
use Arhitector\Transcoder\Traits\OptionsAwareTrait;
use Arhitector\Transcoder\TranscodeInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class Encoder.
 *
 * @package Arhitector\Transcoder\Service
 */
class Encoder implements EncoderInterface
{
	use OptionsAwareTrait;
	
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
	 *
	 * @param TranscodeInterface $media  it may be a stream or media wrapper.
	 * @param FormatInterface    $format new format.
	 * @param array              $options
	 *
	 * @return \Iterator|\Symfony\Component\Process\Process[] returns the instances of 'Process'.
	 * @throws \RuntimeException
	 */
	public function transcoding(TranscodeInterface $media, FormatInterface $format, array $options = [])
	{
		$ffmpegOptions = array_merge([
			'y'      => '',
			'i'      => [$media->getFilePath()],
			'strict' => '-2'
		], $options, $this->getFormatOptions($format), $this->getForceFormatOptions($format));
		
		if ( ! isset($options['output']))
		{
			throw new \RuntimeException('Output file path not found.');
		}
		
		if ( ! isset($options['metadata']) && $format->getTags())
		{
			$ffmpegOptions['metadata'] = $format->getTags();
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
			'metadata',
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
			'metadata'               => '-metadata',
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
		
		if ( ! empty($ffmpegOptions['metadata']))
		{
			$options['map_metadata'] = '-1';
		}
		
		$ffmpegOptions = [];
		
		foreach ($options as $option => $value)
		{
			$ffmpegOptions[] = '-'.ltrim($option, '-');
			
			if (stripos($option, 'filter') !== false)
			{
				$ffmpegOptions[] = implode(', ', (array) $value);
			}
			else if (is_array($value))
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
	 * The options processing.
	 *
	 * @param array $options
	 *
	 * @return array
	 * @throws \Arhitector\Transcoder\Exception\ExecutableNotFoundException
	 */
	protected function resolveOptions(array $options)
	{
		$options += [
			'ffmpeg.path'    => 'ffmpeg',
			'ffmpeg.threads' => 0,
			'timeout'        => 60
		];
		
		if ( ! file_exists($options['ffmpeg.path']) || ! is_executable($options['ffmpeg.path']))
		{
			$options['ffmpeg.path'] = (new ExecutableFinder())->find('ffmpeg', false);
			
			if ( ! $options['ffmpeg.path'])
			{
				throw new ExecutableNotFoundException('Executable not found, proposed ffmpeg', 'ffmpeg');
			}
		}
		
		return $options;
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
			
			if ($format->getFrequency() > 0)
			{
				$options['audio_sample_frequency'] = $format->getFrequency();
			}
			
			if ($format->getChannels() > 0)
			{
				$options['audio_channels'] = $format->getChannels();
			}
		}
		
		if ($format instanceof FrameFormatInterface)
		{
			$options['video_codec'] = (string) $format->getFrameCodec() ?: 'copy';
		}
		
		if ($format instanceof VideoFormatInterface)
		{
			if ($format->getFrameRate() > 0)
			{
				$options['video_frame_rate'] = $format->getFrameRate();
			}
			
			if ($format->getVideoBitrate() > 0)
			{
				$options['video_bitrate'] = $format->getVideoBitrate();
			}
			
			$options['refs'] = 6;
			$options['coder'] = 1;
			$options['sc_threshold'] = 40;
			$options['flags'] = '+loop';
			$options['movflags'] = '+faststart';
			$options['me_range'] = 16;
			$options['subq'] = 7;
			$options['i_qfactor'] = .71;
			$options['qcomp'] = .6;
			$options['qdiff'] = 4;
			$options['trellis'] = 1;
		}
		
		return $options;
	}
	
}
