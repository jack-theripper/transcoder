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
	 * @param string $label
	 *
	 * @return FilterChainInterface
	 */
	public function addInputLabel($label);
	
	/**
	 * Attach other chains as output.
	 *
	 * @param string $label
	 *
	 * @return FilterChainInterface
	 */
	public function addOutputLabel($label);
	
}
