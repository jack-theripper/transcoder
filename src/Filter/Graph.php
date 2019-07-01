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

use SplPriorityQueue;

/**
 * Class Collection.
 *
 * @package Arhitector\Transcoder\Filter
 */
class Graph extends SplPriorityQueue
{
	
	/**
	 * @var int The counter sequence.
	 */
	protected $serial = PHP_INT_MAX;
	
	/**
	 * Graph constructor.
	 */
	public function __construct()
	{
	
	}
	
	/**
	 * Inserts an element in the queue by sifting it up.
	 *
	 * @param FilterInterface $filter
	 * @param mixed           $priority The associated priority.
	 *
	 * @return Graph
	 * @throws \Arhitector\Transcoder\Exception\InvalidFilterException
	 * @throws \InvalidArgumentException
	 */
	public function insert($filter, $priority)
	{
		if ( ! $filter instanceof FilterInterface)
		{
			throw new \InvalidArgumentException('The filter must be an instance of FilterInterface.');
		}
		
		if ($filter instanceof FilterChainInterface)
		{
			parent::insert($filter, [-$priority, $this->serial--]);
		}
		else
		{
			if ($this->isEmpty())
			{
				parent::insert(new FilterChain(), [0, $this->serial--]);
			}
			
			$this->top()->addFilter($filter, $priority);
		}
		
		return $this;
	}
	
	/**
	 * Return current node pointed by the iterator.
	 *
	 * @return FilterInterface
	 */
	public function current()
	{
		return parent::current();
	}
	
	/**
	 * Peeks at the node from the top of the queue.
	 *
	 * @return FilterChainInterface
	 */
	public function top()
	{
		return parent::top();
	}
	
	/**
	 * Extracts a node from top of the heap and sift up.
	 *
	 * @return FilterInterface
	 */
	public function extract()
	{
		return parent::extract();
	}
	
}
