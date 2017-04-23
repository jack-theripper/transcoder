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
namespace Arhitector\Transcoder\Filter;

use Arhitector\Transcoder\Exception\InvalidFilterException;
use Arhitector\Transcoder\Format\FormatInterface;
use Arhitector\Transcoder\TranscodeInterface;

/**
 * Class FilterChain.
 *
 * @package Arhitector\Transcoder\Filter
 */
class FilterChain implements FilterChainInterface
{
	
	/**
	 * @var int Type of input.
	 */
	const TYPE_INPUT = 0;
	
	/**
	 * @var int Type of output.
	 */
	const TYPE_OUTPUT = 1;
	
	/**
	 * @var array List of chains.
	 */
	protected $chains = [];
	
	/**
	 * @var \SplPriorityQueue Collection of filters.
	 */
	protected $filters;
	
	/**
	 * @var string The label of chain.
	 */
	protected $label;
	
	/**
	 * FilterChain constructor.
	 *
	 * @param FilterInterface[] ...$filters
	 *
	 * @throws InvalidFilterException
	 * @throws \InvalidArgumentException
	 */
	public function __construct(FilterInterface ...$filters)
	{
		$this->filters = new \SplPriorityQueue();
		
		foreach ($filters as $filter)
		{
			$this->addFilter($filter, 0);
		}
		
		$this->label = hash('crc32', spl_object_hash($this));
	}
	
	/**
	 * Add a new filter.
	 *
	 * @param FilterInterface $filter
	 * @param int             $priority range 0-99.
	 *
	 * @return FilterChainInterface
	 * @throws \InvalidArgumentException
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0)
	{
		$this->filters->insert($filter, $priority);
		
		return $this;
	}
	
	/**
	 * Attach other chains on the output.
	 *
	 * @param FilterChainInterface $chain
	 * @param mixed                $label
	 *
	 * @return FilterChainInterface
	 */
	public function attach(FilterChainInterface $chain, $label = null)
	{
		$this->chains[] = [self::TYPE_INPUT, $label ?: 0, $chain];
		$chain->update($this, $label);
		
		return $this;
	}
	
	/**
	 * Detach input.
	 *
	 * @param FilterChainInterface $chain
	 *
	 * @return FilterChain
	 */
	public function detach(FilterChainInterface $chain)
	{
		// TODO
		
		return $this;
	}
	
	/**
	 * Receive update from subject.
	 *
	 * @param FilterChainInterface $chain The subject.
	 * @param mixed                $label
	 *
	 * @return FilterChainInterface
	 */
	public function update(FilterChainInterface $chain, $label = null)
	{
		$this->chains[] = [self::TYPE_OUTPUT, $label ?: 0, $chain];
		
		return $this;
	}
	
	/**
	 * Apply filter.
	 *
	 * @param TranscodeInterface $media
	 * @param FormatInterface    $format
	 *
	 * @return array
	 */
	public function apply(TranscodeInterface $media, FormatInterface $format)
	{
		$labels = $this->getLabels();
		$labels[self::TYPE_INPUT] = implode('', $labels[self::TYPE_INPUT]);
		$labels[self::TYPE_OUTPUT] = implode('', $labels[self::TYPE_OUTPUT]);
		$options = [];
		
		foreach (clone $this->filters as $filter)
		{
			$options = array_merge_recursive($options, $filter->apply($media, $format) ?: []);
		}
		
		foreach ($options as $option => &$value)
		{
			if (stripos($option, 'filter') === 0)
			{
				$value = $labels[self::TYPE_INPUT].implode(', ', array_map(function ($value, $filter) {
					return $filter.'='.implode(', '.$filter.'=', (array) $value);
				}, (array) $value, array_keys((array) $value))).$labels[self::TYPE_OUTPUT];
			}
		}
		
		return $options;
	}
	
	/**
	 * Returns the label of chain.
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}
	
	/**
	 * Returns all labels in a format compatible with ffmpeg.
	 *
	 * @return array
	 */
	protected function getLabels()
	{
		$labels = [
			self::TYPE_INPUT  => [],
			self::TYPE_OUTPUT => [],
		];
		
		foreach ($this->chains as list($type, $label, $chain))
		{
			$labels[$type][] = sprintf('[%s:%s]',
				$type == self::TYPE_INPUT ? $chain->getLabel() : $this->getLabel(), $label);
		}
		
		return $labels;
	}
	
}
