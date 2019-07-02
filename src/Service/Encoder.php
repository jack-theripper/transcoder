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
namespace Arhitector\Transcoder\Service;

use Arhitector\Transcoder\Exception\ExecutableNotFoundException;
use Arhitector\Transcoder\Exception\TranscoderException;
use Arhitector\Transcoder\Filter\FilterInterface;
use Arhitector\Transcoder\Format\AudioFormatInterface;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\Format\FrameFormatInterface;
use Arhitector\Transcoder\Format\VideoFormatInterface;
use Arhitector\Transcoder\Traits\ConvertEncodingTrait;
use Arhitector\Transcoder\Traits\OptionsAwareTrait;
use Arhitector\Transcoder\TranscodeInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class Encoder.
 *
 * @package Arhitector\Transcoder\Service
 */
class Encoder implements EncoderInterface
{
	use OptionsAwareTrait, ConvertEncodingTrait;
	
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
			'seek_start'             => '-ss',
			'seek_end'               => '-t'
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
		$heap = new Heap(array_merge([
			'y'              => true,
			'input'          => $media,
			'strict'         => -2,
			'ignore_unknown' => true
		], $this->getForceFormatOptions($format), $this->getFormatOptions($format), $options));
		
		if ( ! $heap->has('output'))
		{
			throw new TranscoderException('Output file path not found.');
		}
		
		if ( ! $heap->has('metadata') && $format->getMetadata())
		{
			$heap->push('metadata', $format->getMetadata());
		}
		
		foreach ($media->getStreams() as $stream)
		{
			$position = false;
			
			/** @var TranscodeInterface $value */
			foreach ($heap->get('input') as $option => $value)
			{
				/*if ($value->getFilePath() == $stream->getFilePath())
				{
					$position = $option;
				}*/
			}
			
			if ($position === false)
			{
				$reflection = new \ReflectionProperty($stream, 'media');
				$reflection->setAccessible(true);
				
				$heap->push('input', $reflection->getValue($stream));
				$position = count($heap->get('input')) - 1;
			}
			
			$heap->push('map', sprintf('%s:%d', $position, $stream->getIndex()));
		}
		
		if (count($heap->get('metadata')) > 0)
		{
			$heap->push('map_metadata', -1);
			//$heap->push('metadata', array_map([$this, 'convertEncoding'], $heap->get('metadata')));
		}
		
		$heaps = [];
		$queue = new \SplQueue();
		
		foreach ($heap->get('input') as $value)
		{
			$queue->push($value);
		}
		
		/** @var TranscodeInterface $localMedia */
		foreach ($queue as $position => $localMedia)
		{
			$reflection = new \ReflectionProperty($localMedia, 'filters');
			$reflection->setAccessible(true);
			
			$localHeap = new Heap(['input' => (string) $localMedia->getSource()]);
			
			/** @var FilterInterface $filter */
			foreach (clone $reflection->getValue($localMedia) as $filter)
			{
				foreach ($filter->apply($localMedia, $format) as $option => $values)
				{
					if ($option == 'input')
					{
						$queue->push($values[0]);
						
						continue;
					}
					
					foreach ((array) $values as $value)
					{
						if (stripos($option, 'filter') !== false)
						{
							if ($queue->count() > 1 && stripos($option, 'filter_complex') === false)
							{
								$option = str_replace('filter', 'filter_complex', $option);
							}
							
							// [file_index:stream_specifier]
							// -map [-]input_file_id[:stream_specifier][?][,sync_file_id[:stream_specifier]] | [linklabel]
							$heap->push($option, $value);
							
							continue;
						}
						
						$localHeap->push($option, $value);
					}
				}
			}
			
			$heaps[$position] = $localHeap;
		}
		
		$filePath = $heap->get('output');
		$options = array_merge(...iterator_to_array($heap));
		
		if (($position = array_search('-i', $options)) !== false) // inject inputs to list of options
		{
			foreach (array_reverse($heaps) as $heap)
			{
				array_splice($options, $position + 1, 0, array_merge(...iterator_to_array($heap)));
			}
			
			unset($options[$position]);
		}
		
		if ($format->getPasses() > 1)
		{
			// TODO: FFMpeg создает файлы вида <filename>-0.log, чтобы их очистить проще создать папку.
			$options[] = '-passlogfile';
			$options[] = tempnam(sys_get_temp_dir(), 'ffmpeg');
		}
		
		for ($pass = 1; $pass <= $format->getPasses(); ++$pass)
		{
			$_options = $options;
			
			if ($format->getPasses() > 1)
			{
				$_options[] = '-pass';
				$_options[] = $pass;
			}
			
			$_options[] = (string) $filePath[0];
			$process = (new Process(array_merge([$this->options['ffmpeg.path']], $_options)))
				->setTimeout($this->options['timeout']);
			
			$format->emit('before.pass', $media, $format, $process);
			
			yield $pass => $process;
			
			$format->emit('after.pass', $media, $format, $process);
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
	 * Returns the options without aliases.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	protected function resolveOptionsAlias(array $options)
	{
		$haystack = array_diff_key($options, $this->getAliasOptions() + ['output' => null]);
		
		foreach ($this->getAliasOptions() as $option => $value)
		{
			if (isset($options[$option]))
			{
				$haystack[$value] = $options[$option];
				
				if (is_bool($options[$option]))
				{
					$haystack[$value] = '';
				}
			}
		}
		
		return $haystack;
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
		$options = $format->getOptions();
		
		if ($format instanceof AudioFormatInterface)
		{
			$options['audio_codec'] = (string) $format->getAudioCodec() ?: 'copy';
			
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
			
			$options['movflags'] = '+faststart';
		}
		
		return $options;
	}
	
}
