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
	 * @var \SplPriorityQueue|FilterInterface[] The collection of filters.
	 */
	protected $filters;
	
	/**
	 * @var array List of inputs.
	 */
	protected $inputs = [];
	
	/**
	 * @var array List of outputs.
	 */
	protected $outputs = [];
	
	/**
	 * @var int The counter sequence.
	 */
	protected $serial = PHP_INT_MAX;
	
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
		$this->filters->insert($filter, [-$priority, $this->serial--]);
		
		return $this;
	}
	
	/**
	 * Attach other chains as input.
	 *
	 * @param string $label
	 *
	 * @return FilterChainInterface
	 */
	public function addInputLabel($label)
	{
		$this->inputs[] = (string) $label;
		
		return $this;
	}
	
	/**
	 * Attach other chains as output.
	 *
	 * @param string $label
	 *
	 * @return FilterChainInterface
	 */
	public function addOutputLabel($label)
	{
		$this->outputs[] = (string) $label;
		
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
		$options = [];
		
		foreach (clone $this->filters as $filter)
		{
			foreach ($filter->apply($media, $format) as $option => $value)
			{
				if (stripos($option, 'filter') !== false)
				{
					$option = 'filter';
				}
				
				$options[$option][] = $value;
			}
		}
		
		if (isset($options['filter']))
		{
			$option = [];
			
			foreach (array_merge_recursive(...$options['filter']) as $filter => $value)
			{
				$option[] = $filter.'='.implode(', '.$filter.'=', (array) $value);
			}
			
			$options['filter'] = implode('', $this->inputs).implode(', ', $option).implode('', $this->outputs);
		}
		
		return $options;
	}
	
}
