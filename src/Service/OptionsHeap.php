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
 * Class OptionsHeap.
 *
 * @package Arhitector\Transcoder\Service
 */
class OptionsHeap extends \SplHeap
{
	
	/**
	 * Return current node pointed by the iterator
	 *
	 * @return mixed The current node value.
	 */
	public function current()
	{
		list($option, $value) = parent::current();
		
		$option = $option[0] == '-' ? $option : '-'.$option;
		
		if ( ! is_scalar($value))
		{
			if (stripos($option, 'filter') === 1)
			{
				return [$option, implode('; ', (array) $value)];
			}
			
			$results = [];
			
			foreach ((array) $value as $key => $item)
			{
				$results[] = $option;
				$results[] = is_int($key) ? $item : sprintf('%s=%s', $key, $item);
			}
			
			return $results;
		}
		
		if ($value || is_int($value))
		{
			return [$option, $value];
		}
		
		return [$option];
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
		$options = ['y', 'ss', 'i'];
		
		if (in_array(ltrim($value1[0], '-'), $options, false))
		{
			if (in_array(ltrim($value2[0]), $options, false))
			{
				return 0;
			}
			
			return 1;
		}
		
		return -1;
	}
	
}
