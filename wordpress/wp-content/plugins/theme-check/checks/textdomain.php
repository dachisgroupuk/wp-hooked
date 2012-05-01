<?php

class TextDomainCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		global $data, $themename;
		$ret = true;
		$error = '';
		checkcount();
		if ( $data['Name'] === 'Twenty Ten' || $data['Name'] === 'Twenty Eleven')
			return $ret;

		$checks = array(
		'/[\s|\(]_[e|_]\s?\(\s?[\'|"][^\'|"]*[\'|"]\s?\)/' => __( 'You have not included a text domain!', 'themecheck' )
		 );

		foreach ( $php_files as $php_key => $phpfile ) {
			$error = '';
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match_all( $key, $phpfile, $matches ) || preg_match_all( '/[\s|\(]_x\s?\(\s?[\'|"][^\'|"]*[\'|"]\s?,\s?[\'|"][^\'|"]*[\'|"]\s?\)/', $phpfile, $matches )) {
				
					$filename = tc_filename( $php_key );
					foreach ($matches[0] as $match ) {
						$grep = tc_grep( ltrim( $match ), $php_key );
						preg_match( '/[^\s]*\s[0-9]+/', $grep, $line);
						$error .= ( !strpos( $error, $line[0] ) ) ? $grep : '';		
					}
				$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: Text domain problems in <strong>{$filename}</strong>. {$check}{$error}", "themecheck" );
				}
			}
		}

		$checks = array(
		'/[\s|\(]_[e|_]\s?\([^,|;]*\s?,\s?[\'|"]([^\'|"]*)[\'|"]\s?\)/' => __( 'Text domain should match theme slug: <strong>' . $themename . '</strong>', 'themecheck' ),
		'/[\s|\(]_x\s?\([^,]*\s?,\s[^\'|"]*[\'|"][^\'|"]*[\'|"],\s?[\'|"]([^\'|"]*)[\'|"]\s?\)/' => __( 'Text domain should match theme slug: <strong>' . $themename . '</strong>', 'themecheck' )
		 );
		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match_all( $key, $phpfile, $matches ) ) {
					foreach ($matches[0] as $count => $domaincheck) {
						if ( preg_match( '/[\s|\(]_[e|_]\s?\(\s?[\'|"][^\'|"]*[\'|"]\s?\)/', $domaincheck ) )
							unset( $matches[1][$count] ); //filter out false positives
					}
					$filename = tc_filename( $php_key );
					$count = 0;
					while ( isset( $matches[1][$count] ) ) {
						if ( $matches[1][$count] !== $themename ) {
							$error = tc_grep( $matches[0][$count], $php_key );
							if ( $matches[1][$count] === 'twentyten' || $matches[1][$count] === 'twentyeleven' ):
								$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: Text domain problems in <strong>{$filename}</strong>. The twentyten text domain is being used!{$error}", "themecheck" );
							else:
							if ( defined( 'TC_TEST' ) && strpos( strtolower( $themename ), $matches[1][$count] ) === false ) {
								$error = tc_grep( $matches[0][$count], $php_key );
								$this->error[] = __( "<span class='tc-lead tc-recommended'>RECOMMENDED</span>: Text domain problems in <strong>{$filename}</strong>. {$check} You are using: <strong>{$matches[1][$count]}</strong>{$error}", "themecheck" );
							}
							endif;
						}
					$count++;
					} //end while
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new TextDomainCheck;