<?php

namespace wrossmann\Costrenc;

abstract class Costrenc extends \php_user_filter {
	
	protected $in_charset, $out_charset;
	private $buffer = '';
	private $total_consumed = 0;
	
	public function filter($in, $out, &$consumed, $closing) {
		$output = '';

		while ($bucket = stream_bucket_make_writeable($in)) {
			$input = $this->buffer . $bucket->data;
			for( $i=0, $p=0; ($c=mb_substr($input, $i, 1, $this->in_charset)) !== ""; ++$i, $p+=strlen($c) ) {
				$output .= mb_convert_encoding($c, $this->out_charset, $this->in_charset);
			}
			$this->buffer = substr($input, $p);
			$consumed += $p;
		}

		// this means that  there's unconverted data at the end of the bridage.
		if( $closing && strlen($this->buffer) > 0 ) {
			$this->raise_error( sprintf(
				"Likely encoding error at offset %d in input stream, subsequent data may be malformed or missing.",
				$this->total_consumed += $consumed)
			);
			$consumed += strlen($this->buffer);
			// give it the ol' college try
			$output .= mb_convert_encoding($this->buffer, $this->out_charset, $this->in_charset);
		}

		$this->total_consumed += $consumed;

		if ( ! isset($bucket) ) {
			$bucket = stream_bucket_new($this->stream, $output);
		} else {
			$bucket->data = $output;
		}
		stream_bucket_append($out, $bucket);
		return PSFS_PASS_ON;
	}

	protected function raise_error($message) {
		user_error( sprintf(
			"%s[%s]: %s",
			__CLASS__, get_class($this), $message
		), E_USER_WARNING);
	}
	
}

