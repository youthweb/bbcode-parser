<?php
/*
 * This file is part of the Youthweb\BBCodeParser package.
 *
 * (c) Youthweb e.V. <info@youthweb.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Youthweb\BBCodeParser;

/**
 * Validation Interface
 */

interface ValidationInterface
{
	/**
	 * Validate an email
	 *
	 * @param   string
	 * @return  bool
	 */
	public function isValidEmail($val);

	/**
	 * Validate an url
	 *
	 * @param   string
	 * @return  bool
	 */
	public function isValidUrl($val);

	/**
	 * Validate an image url
	 *
	 * @param   string
	 * @return  bool
	 */
	public function isValidImageUrl($val);
}
