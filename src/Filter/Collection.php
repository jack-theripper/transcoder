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
namespace Arhitector\Jumper\Filter;

use SplPriorityQueue;

/**
 * Class Collection.
 *
 * @package Arhitector\Jumper\Filter
 */
class Collection extends SplPriorityQueue
{
	
	/**
	 * Inserts an element in the queue by sifting it up.
	 *
	 * @param FilterInterface $filter
	 * @param mixed           $priority The associated priority.
	 *
	 * @return Collection
	 * @throws \InvalidArgumentException
	 */
	public function insert($filter, $priority)
	{
		if ( ! $filter instanceof FilterInterface)
		{
			throw new \InvalidArgumentException('The filter must be an instance of FilterInterface.');
		}
		
		parent::insert($filter, $priority);
		
		return $this;
	}
	
}
