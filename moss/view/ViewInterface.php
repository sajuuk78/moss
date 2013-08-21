<?php
namespace moss\view;

/**
 * Moss ViewInterface
 *
 * @package Moss View
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface ViewInterface extends \ArrayAccess, \Countable {

	/**
	 * Assigns template to view
	 *
	 * @param string $template path to template (supports namespaces)
	 *
	 * @return ViewInterface
	 */
	public function template($template);

	/**
	 * Sets variable to be used in template
	 *
	 * @param string|array $offset variable name, if array - its key will be used as variable names
	 * @param null|mixed   $value variable value
	 *
	 * @return ViewInterface
	 * @throws \InvalidArgumentException
	 */
	public function set($offset, $value = null);

	/**
	 * Retrieves variable value
	 *
	 * @param string $offset variable name
	 *
	 * @return mixed
	 * @throws \OutOfRangeException
	 */
	public function get($offset);

	/**
	 * Renders view
	 *
	 * @return string
	 * @throws \InvalidArgumentException
	 */
	public function render();

	/**
	 * Renders and returns view as string
	 *
	 * @return string
	 */
	public function __toString();
}