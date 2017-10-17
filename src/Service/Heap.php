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

/**
 * Class Heap
 *
 * @package Arhitector\Transcoder\Service
 */
class Heap extends \SplHeap
{
	
	/**
	 * @var array
	 */
	protected $heap = [];
	
	/**
	 * Heap constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		foreach ($options as $option => $value)
		{
			$this->push($option, $value);
		}
	}
	
	/**
	 * Pushes an element at the end of the doubly linked list.
	 *
	 * @param string $option
	 * @param mixed  $value
	 */
	public function push($option, $value)
	{
		if ( ! is_string($option))
		{
			throw new \InvalidArgumentException('The option value must be a string type.');
		}
		
		$this->heap[$option][] = $value;
	}
	
	/**
	 * Checks whether the key exists.
	 *
	 * @param string $option
	 *
	 * @return bool
	 */
	public function has($option)
	{
		return array_key_exists($option, $this->heap);
	}
	
	/**
	 * Returns a value by key.
	 *
	 * @param $option
	 *
	 * @return array|mixed
	 */
	public function get($option)
	{
		if ($this->has($option))
		{
			return $this->heap[$option];
		}
		
		return [];
	}
	
	/**
	 * Rewind iterator back to the start (no-op)
	 *
	 * @return void
	 */
	public function rewind()
	{
		foreach ($this->heap as $option => $value)
		{
			$this->insert([$option, $value]);
		}
		
		parent::rewind();
	}
	
	/**
	 * Return current node pointed by the iterator
	 *
	 * @return mixed
	 */
	public function current()
	{
		list($option, $values) = parent::current();
		
		if (isset($this->getAliasOptions()[ltrim($option, '-')]))
		{
			$option = $this->getAliasOptions()[ltrim($option, '-')];
		}
		
		$option = $option[0] == '-' ? $option : '-'.$option;
		
		if ($option == '-output')
		{
			return [];
		}
		
		$options = [];
		
		if ($option == '-map' || $option == '-metadata')
		{
			foreach ($option == '-metadata' ? array_merge(...$values) : $values as $key => $value)
			{
				$options[] = $option;
				$options[] = is_int($key) ? $value : sprintf('%s=%s', $key, $value);
			}
		}
		else if (stripos($option, 'filter') === 1)
		{
			// array_merge_recursive(...$values)
			$options[] = $option;
			$options[] = implode('; ', $values);
		}
		else if (($value = array_pop($values)) !== false)
		{
			$options[] = $option;
			
			if (is_scalar($value) && trim($value) != '' && $value !== true)
			{
				$options[] = $value;
			}
		}
		
		return $options;
	}
	
	private function isStackable($option)
	{
		return in_array($option, ['-map', /*'-input'*/]);
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
	 * Compare elements in order to place them correctly in the heap while sifting up.
	 *
	 * @param mixed $value1 The value of the first node being compared.
	 * @param mixed $value2 The value of the second node being compared.
	 *
	 * @return int Result of the comparison.
	 */
	protected function compare($value1, $value2)
	{
		$haystack = [
			'y',
			'ignore_unknown',
			'stream_loop',
			'sseof',
			'itsoffset',
			'thread_queue_size',
			'seek_timestamp',
			'accurate_seek',
			'noaccurate_seek',
			'ss',
			'seek_start',
			'i',
			'input',
		
		];
		
		if (($value1 = array_search(ltrim($value1[0], '-'), $haystack, false)) !== false)
		{
			return ($value2 = array_search(ltrim($value2[0], '-'), $haystack)) !== false && $value1 > $value2 ? -1 : 1;
		}
		
		return -1;
	}
	
}
