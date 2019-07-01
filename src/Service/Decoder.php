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

use Arhitector\Transcoder\Codec;
use Arhitector\Transcoder\Exception\ExecutableNotFoundException;
use Arhitector\Transcoder\Exception\TranscoderException;
use Arhitector\Transcoder\Traits\OptionsAwareTrait;
use Arhitector\Transcoder\TranscodeInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class Decoder.
 *
 * @package Arhitector\Transcoder\Service
 */
class Decoder implements DecoderInterface
{
	use OptionsAwareTrait;
	
	/**
	 * Decoder constructor.
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
	 * Demultiplexing.
	 *
	 * @param TranscodeInterface $media
	 *
	 * @return \stdClass
	 * @throws \Symfony\Component\Process\Exception\InvalidArgumentException
	 *
	 * <code>
	 * object(stdClass)[5]
	 *  public 'format' => array (size=13)
	 *      'start_time' => string '0.025056' (length=8)
	 *      'duration' => string '208.535510' (length=10)
	 *  public 'streams' => array (size=2)
	 *      0 => array (size=24)
	 *          'frequency' => int 44100
	 *          'channels' => int 2
	 *          'index' => int 0
	 *          'type' => string 'audio' (length=5)
	 *  ...
	 * </code>
	 *
	 * @throws \InvalidArgumentException
	 * @throws \Symfony\Component\Process\Exception\ProcessFailedException
	 * @throws \Symfony\Component\Process\Exception\RuntimeException
	 * @throws \Symfony\Component\Process\Exception\LogicException
	 * @throws \Arhitector\Transcoder\Exception\TranscoderException
	 */
	public function demuxing(TranscodeInterface $media)
	{
		$output = (new Process([
			$this->options['ffprobe.path'],
			'-loglevel',
			'quiet',
			'-print_format',
			'json',
			'-show_format',
			'-show_streams',
			'-show_error',
			'-i',
			'-'
		]))
			->setInput($media->getSource())
			->mustRun()
			->getOutput();
		
		if ( ! ($output = json_decode($output, true)) || isset($output['error']))
		{
			if ( ! isset($output['error']['string']))
			{
				$output['error']['string'] = 'Unable to parse ffprobe output.';
			}
			
			throw new TranscoderException($output['error']['string']);
		}
		
		$result = new \stdClass;
		$result->format = $this->ensureFormatProperties($output['format']);
		$result->streams = [];
		
		foreach ((array) $output['streams'] as $stream)
		{
			$result->streams[] = $this->resolveStreamProperties($stream);
		}
		
		return $result;
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
			'ffprobe.path' => 'ffprobe',
			'timeout'      => 60
		];
		
		if ( ! file_exists($options['ffprobe.path']) || ! is_executable($options['ffprobe.path']))
		{
			$options['ffprobe.path'] = (new ExecutableFinder())->find('ffprobe', false);
			
			if ( ! $options['ffprobe.path'])
			{
				throw new ExecutableNotFoundException('Executable not found, proposed ffprobe', 'ffprobe');
			}
		}
		
		return $options;
	}
	
	/**
	 * Returns the format properties.
	 *
	 * @param array $properties
	 *
	 * @return array
	 */
	protected function ensureFormatProperties(array $properties)
	{
		// defaults keys for transforming
		$properties += [
			'bit_rate'    => 0,
			'duration'    => 0.0,
			'format_name' => '',
			'tags'        => [],
			'codecs'      => []
		];
		
		if ( ! empty($this->options[ServiceFactoryInterface::OPTION_TEST_CODECS]))
		{
			$properties['codecs'] = $this->getAvailableCodecs();
		}
		
		$properties['metadata'] = (array) $properties['tags'];
		$properties['format'] = $properties['format_name'];
		$properties['bitrate'] = $properties['bit_rate'];
		
		unset($properties['tags'], $properties['bit_rate']);
		
		return $properties;
	}
	
	/**
	 * Returns the stream properties.
	 *
	 * @param array $properties
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	protected function resolveStreamProperties(array $properties)
	{
		// defaults keys for transforming
		$properties += [
			'tags'            => [],
			'sample_rate'     => 0,
			'codec_name'      => null,
			'codec_long_name' => '',
			'type'            => $properties['codec_type'],
			'frequency'       => 0,
			'channels'        => 1
		];
		
		$properties['metadata'] = (array) $properties['tags'];
		$properties['frequency'] = (int) $properties['sample_rate'];
		$properties['codec'] = new Codec($properties['codec_name'], $properties['codec_long_name']);
		$properties['frame_rate'] = isset($properties['r_frame_rate']) ? $properties['r_frame_rate'] : 0.0;
		
		if (strpos($properties['frame_rate'], '/') > 0)
		{
			list($val1, $val2) = explode('/', $properties['frame_rate']);
			
			if ($val1 > 0 && $val2 > 0)
			{
				$properties['frame_rate'] = (float) $val1 / (float) $val2;
			}
		}
		
		return $properties;
	}
	
	/**
	 * Supported codecs.
	 *
	 * @return int[]
	 */
	public function getAvailableCodecs()
	{
		$codecs = [];
		$bit = ['.' => 0, 'A' => 1, 'V' => 2, 'S' => 4, 'E' => 8, 'D' => 16];
		
		try
		{
			foreach (['encoders', 'codecs'] as $command)
			{
				$process = new Process(sprintf('"%s" "-%s"', $this->options['ffprobe.path'], $command));
				$process->start();
				
				while ($process->getStatus() !== Process::STATUS_TERMINATED)
				{
					usleep(200000);
				}
				
				if (preg_match_all('/\s([VASFXBDEIL\.]{6})\s(\S{3,20})\s/', $process->getOutput(), $matches))
				{
					if ($command == 'encoders')
					{
						foreach ($matches[2] as $key => $value)
						{
							$codecs[$value] = $bit[$matches[1][$key]{0}] | $bit['E'];
						}
					}
					else // codecs, encoders + decoders
					{
						foreach ($matches[2] as $key => $value)
						{
							$key = $matches[1][$key];
							$codecs[$value] = $bit[$key{2}] | $bit[$key{0}] | $bit[$key{1}];
						}
					}
				}
			}
		}
		catch (\Exception $exception)
		{
		
		}
		
		return $codecs;
	}
	
}
