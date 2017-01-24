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

use Arhitector\Jumper\Codec;
use Arhitector\Jumper\Exception\ExecutableNotFoundException;
use Arhitector\Jumper\Exception\TranscoderException;
use Arhitector\Jumper\TranscoderInterface;
use Symfony\Component\Process\Process;

/**
 * Class Decoder.
 *
 * @package Arhitector\Jumper\Service
 */
class Decoder
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
	 * @param TranscoderInterface $media
	 *
	 * @return \stdClass
	 * @throws \InvalidArgumentException
	 * @throws \Symfony\Component\Process\Exception\ProcessFailedException
	 * @throws \Symfony\Component\Process\Exception\RuntimeException
	 * @throws \Symfony\Component\Process\Exception\LogicException
	 * @throws \Arhitector\Jumper\Exception\TranscoderException
	 */
	public function demuxing(TranscoderInterface $media)
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
		$result->format = $this->resolveFormatProperties($output['format']);
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
	 * @throws \Arhitector\Jumper\Exception\ExecutableNotFoundException
	 */
	protected function resolveOptions(array $options)
	{
		return array_merge([
			'ffprobe.path' => 'ffprobe',
			'timeout'      => 60
		], $options);
	}
	
	/**
	 * Returns the format properties.
	 *
	 * @param array $properties
	 *
	 * @return array
	 */
	protected function resolveFormatProperties(array $properties)
	{
		// defaults keys for transforming
		$properties += [
			'bit_rate'    => 0,
			'duration'    => 0.0,
			'format_name' => '',
			'format'      => null,
			'tags'        => []
		];
		
		$properties['tags'] = (array) $properties['tags'];
		$properties['format'] = $properties['format_name'];
		
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
		$defaults = [
			'frequency'   => 0,
			'channels'    => 1,
			'sample_rate' => 0,
			'index'      => 0,
			'type'       => $properties['codec_type'],
			'profile'    => '',
			'bit_rate'   => 0,
			'start_time' => 0.0,
			'duration'   => 0.0,
			'tags'       => [],
			'properties' => []
		];
		
		$properties = array_merge($defaults, $properties);
		$properties['properties'] = (array) $properties['tags'];
		$properties['frequency'] = (int) $properties['sample_rate'];
		$properties['codec'] = new Codec($properties['codec_name'], $properties['codec_long_name']);
		
		unset($properties['sample_rate'], $properties['disposition'], $properties['tags']);
		
		return $properties;
	}
	
}
