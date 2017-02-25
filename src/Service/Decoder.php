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
		$output = (new Process(sprintf('%s -loglevel quiet -print_format json -show_format -show_streams -show_error -i %s',
			$this->options['ffprobe.path'], $media->getFilePath()), null, null, null, $this->options['timeout']))
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
			'tags'        => []
		];
		
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
		
		return $properties;
	}
	
}
