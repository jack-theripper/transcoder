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

/**
 * Interface FilterChainInterface.
 *
 * @package Arhitector\Transcoder\Filter
 */
interface FilterChainInterface extends AudioFilterInterface, FrameFilterInterface
{
	
	/**
	 * Add a new filter.
	 *
	 * @param FilterInterface $filter
	 * @param int             $priority range 0-99.
	 *
	 * @return FilterChainInterface
	 * @throws InvalidFilterException
	 */
	public function addFilter(FilterInterface $filter, $priority = 0);
	
	/**
	 * Attach other chains as input.
	 *
	 * @param FilterChainInterface $chain
	 * @param mixed                $label
	 *
	 * @return FilterChainInterface
	 */
	public function attach(FilterChainInterface $chain, $label = null);
	
	/**
	 * Detach input.
	 *
	 * @param FilterChainInterface $chain
	 *
	 * @return FilterChainInterface
	 */
	public function detach(FilterChainInterface $chain);
	
	/**
	 * Receive update from subject.
	 *
	 * @param FilterChainInterface $chain The subject.
	 * @param mixed                $label
	 *
	 * @return FilterChainInterface
	 */
	public function update(FilterChainInterface $chain, $label = null);
	
	/**
	 * Returns the label of chain.
	 *
	 * @return string
	 */
	public function getLabel();
	
}
