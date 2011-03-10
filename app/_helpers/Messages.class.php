<?php
/**
 * Custom messages class
 */
class Messages {
    
	/**
	 * Constant for how the message will be formatted. Final string will be created via sprintf() and appropriate
	 * values substitued into the template 
	 */
	const MESSAGE_TEMPLATE = '<div class="message %s">%s</div>';
	
	/**
	 * @var array Array that holds all messages
	 */
	private static $messages = array();
	
	
	/**
	 * Merges messages from a different array (such as the SESSION).  Appends the array to the current messages array
	 * @param array $array The array to merge
	 */
	public function mergeMessages($array) {
		if(is_array($array)) {
			self::$messages = array_merge(self::$messages, $array);
		}
	}


	/**
	 * Adds a message to the array as well as the SESSION messages array
	 * @param string $text The message to add to the array
	 * @param string $classes Space separated list of classes to apply to the message
	 */
	public function addMessage($text, $classes = 'default') {
		array_push(self::$messages, array('text' => $text, 'classes' => $classes));
	}
	
	
	/**
	 * Get the messages array
	 * @return array Returns the current array of messages
	 */
	public function getMessagesArray() {
		return self::$messages;
	}
	
	
	/**
	 * Clear the messages array
	 * @return bool Returns true
	 */
	public function clearMessageArray() {
		self::$messages = array();
	}
	
	
	/**
	 * Build the HTML string for a single message
	 * @param string $text The text of the message
	 * @param string $classes Space separated list of classes to apply to the message
	 * @return string Returns the Html string
	 */
	public function buildMessage($text, $classes = 'default') {
		return sprintf(self::MESSAGE_TEMPLATE, $classes, $text);
	}
	
	
	/**
	 * Get the messages as a concatenated HTML string
	 * @return string The concatenated HTML string of all messages
	 */
	public function getMessagesHtml() {
		$html = '';
		
		foreach(self::$messages as $k => $v) {
			$html .= self::buildMessage($v['text'], $v['classes']);
		}
		
		return $html;
	}
	
	
	/**
	 * Print a message immediately
	 * @param string $text The text of the message
	 * @param string $classes Space separated list of classes to apply to the message
	 * @return mixed Outputs the message immediately via an echo()
	 */
	public function printMessage($text, $classes = 'default') {
		$message = self::buildMessage($text, $classes);
		echo($message);
	}


}