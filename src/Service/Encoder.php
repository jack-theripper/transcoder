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
use Arhitector\Transcoder\Exception\TranscoderException;
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
	 * The alias of options.
	 *
	 * @return array
	 */
	public function getAliasOptions()
	{
		return [
			'input'                  => '-i',
			'disable_audio'          => '-an',
			'disable_video'          => '-vn',
			'disable_subtitle'       => '-sn',
			'audio_quality'          => '-qscale:a',
			'audio_codec'            => '-codec:a',
			'audio_bitrate'          => '-b:a',
			'audio_sample_frequency' => '-ar',
			'audio_channels'         => '-ac',
			'video_quality'          => '-qscale:v',
			'video_codec'            => '-codec:v',
			'video_aspect_ratio'     => '-aspect',
			'video_frame_rate'       => '-r',
			'video_max_frames'       => '-vframes',
			'video_bitrate'          => '-b:v',
			'video_pixel_format'     => '-pix_fmt',
			'metadata'               => '-metadata',
			'force_format'           => '-f',
			'seek_start'             => 'ss',
			'seek_end'               => 't'
		];
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
		$_options = array_merge_recursive(array_merge([
			'y'      => '',
			'input'  => [$media->getFilePath()],
			'strict' => '-2'
		], $this->getForceFormatOptions($format), $this->getFormatOptions($format)), $options);
		
		if ( ! isset($options['output']))
		{
			throw new TranscoderException('Output file path not found.');
		}
		
		$filePath = $options['output'];
		
		if ( ! isset($options['metadata']) && $format->getTags())
		{
			$_options['metadata'] = $format->getTags();
		}
		
		foreach ($media->getStreams() as $stream)
		{
			if (($input = array_search($stream->getFilePath(), $_options['input'], false)) === false)
			{
				$_options['input'][] = $stream->getFilePath();
				$input = count($_options['input']) - 1;
			}
			
			$_options['map'][] = sprintf('%s:%d', $input, $stream->getIndex());
		}
		
		// получаем чистый массив опций без псевдонимов.
		$options = array_diff_key($_options, $this->getAliasOptions() + ['output' => null]);
		
		foreach ($this->getAliasOptions() as $option => $value)
		{
			if (isset($_options[$option]))
			{
				$options[$value] = $_options[$option];
				
				if (is_bool($_options[$option]))
				{
					$options[$value] = '';
				}
			}
		}
		
		if ( ! empty($_options['metadata']))
		{
			$options['map_metadata'] = '-1';
		}
		
		$_options = [];
		
		foreach ($options as $option => $value)
		{
			$_options[] = $option[0] == '-' ? $option : '-'.$option;
			
			if ( ! is_scalar($value))
			{
				if (stripos($option, 'filter') === 0)
				{
					$_options[] = implode('; ', (array) $value);
				}
				else
				{
					array_pop($_options);
					
					foreach ((array) $value as $_option => $_value)
					{
						$_options[] = $option[0] == '-' ? $option : '-'.$option;
						$_options[] = is_int($_option) ? $_value : sprintf('%s=%s', $_option, $_value);
					}
				}
			}
			else if ($value || is_int($value))
			{
				$_options[] = $value;
			}
		}
		
		if ($format->getPasses() > 1)
		{
			// TODO: FFMpeg создает файлы вида <filename>-0.log, чтобы их очистить проще создать папку.
			$_options[] = '-passlogfile';
			$_options[] = tempnam(sys_get_temp_dir(), 'ffmpeg');
		}
		
		for ($pass = 1; $pass <= $format->getPasses(); ++$pass)
		{
			$options = $_options;
			
			if ($format->getPasses() > 1)
			{
				$options[] = '-pass';
				$options[] = $pass;
			}
			
			$options[] = $filePath;
			
			yield (new ProcessBuilder($options))
				->setPrefix($this->options['ffmpeg.path'])
				->setTimeout($this->options['timeout'])
				->getProcess();
		}
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
			'timeout'        => 0
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
		$formatExtensions = $format->getExtensions();
		
		if (count($formatExtensions) > 0)
		{
			if ( ! file_exists(__DIR__.'/../../bin/ffmpeg_fmt.php'))
			{
				return [];
			}
			
			$ffmpeg_fmt = (array) require __DIR__.'/../../bin/ffmpeg_fmt.php';
			
			foreach ($ffmpeg_fmt as $format_string => $extensions)
			{
				if (array_intersect($formatExtensions, $extensions))
				{
					return ['force_format' => $format_string];
				}
			}
		}
		
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
			$options['video_codec'] = (string) $format->getVideoCodec() ?: 'copy';
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
