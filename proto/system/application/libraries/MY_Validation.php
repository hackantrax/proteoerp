<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Validation extends CI_Validation
{

	var $_dataobject;

	

	/**
	 * Unique
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function unique($str)
	{
    return ( $this->_dataobject->is_unique($this->_current_field, $str) );
	}
	

	/**
	 * captcha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function captcha($str)
	{
    return ( strtolower($_SESSION["captcha"]) == strtolower($str) );
	}


	function valid_email($email){
		$email=trim($email);
		if(strlen($email)>0)
			return parent::valid_email($email);
		else
			return TRUE;
		
	}

	function chfecha($validar,$format=null,$fname=null){
		$formato= (empty($format))? RAPYD_DATE_FORMAT: $format;
		$fnombre= (empty($fname)) ? 'chfecha' : $fname;
		$formato=preg_quote($formato,'/');

		$search[] = "d"; $replace[] = "(0[1-9]|[1-2][0-9]|3[0-1])";
		$search[] = "j"; $replace[] = "([1-9]|[1-2][0-9]|3[0-1])";
		$search[] = "m"; $replace[] = "(0[1-9]|1[0-2])";
		$search[] = "n"; $replace[] = "([1-9]|1[0-2])";
		$search[] = "Y"; $replace[] = "([0-9]{4})";
		$search[] = "y"; $replace[] = "([0-9]{2})";
		$search[] = "H"; $replace[] = "([0-1][0-9]|2[0-4])";
		$search[] = "i"; $replace[] = "(0[0-9]|[1-5][0-9]|60)";
		$search[] = "s"; $replace[] = "(0[0-9]|[1-5][0-9]|60)";
		$pattern = str_replace($search, $replace, $formato);
		$pattern = '/'.$pattern.'/';
		$replace = $search = array();

		if(preg_match($pattern,$validar)>0){
			$search[] = "j"; $replace[] = "(?P<i>\d+)";
			$search[] = "d"; $replace[] = "(?P<i>\d+)";
			$search[] = "m"; $replace[] = "(?P<e>\d+)";
			$search[] = "n"; $replace[] = "(?P<e>\d+)";
			$search[] = "Y"; $replace[] = "(?P<a>\d+)";
			$search[] = "y"; $replace[] = "(?P<a>\d+)";

			$pattern = str_replace($search, $replace, $formato);
			$pattern = '/'.$pattern.'/';

			preg_match($pattern,$validar,$matches);

			$dia =(isset($matches['i']))? $matches['i'] : 1;
			$mes =(isset($matches['e']))? $matches['e'] : 1;
			$anio=(isset($matches['a']))? $matches['a'] : 1;

			if(!checkdate($mes,$dia,$anio)){
				$this->set_message($fnombre, "La fecha introducida en el campo <b>%s</b> no es v&aacute;lida");
				return false;
			}
		}else{
			$this->set_message($fnombre, "La fecha introducida en el campo <b>%s</b> no coincide con el formato");
			return false;
		}
		return true;
	}


	/**
	 * Corre las validaciones
	 *
	 * Fue modifica con respecto a la original para soportar 
	 * campos requeridos condicionales.
	 * @access	public
	 * @return	bool
	 */		
	function run()
	{
		// Do we even have any data to process?  Mm?
		if (count($_POST) == 0 OR count($this->_rules) == 0)
		{
			return FALSE;
		}
	
		// Load the language file containing error messages
		$this->CI->lang->load('validation');
							
		// Cycle through the rules and test for errors
		foreach ($this->_rules as $field => $rules)
		{
			//Explode out the rules!
			$ex = explode('|', $rules);

			// Is the field required?  If not, if the field is blank  we'll move on to the next test
			if ( ! in_array('required', $ex, TRUE))
			{
				if ( ! isset($_POST[$field]) OR $_POST[$field] == '')
				{
					$clave=array_search('condi_required',$ex);
					if($clave !== false ) unset($ex[$clave]); else continue;
					//if( ! in_array('condi_required', $ex, TRUE)) continue;
				}
			}
			
			/*
			 * Are we dealing with an "isset" rule?
			 *
			 * Before going further, we'll see if one of the rules
			 * is to check whether the item is set (typically this
			 * applies only to checkboxes).  If so, we'll
			 * test for it here since there's not reason to go
			 * further
			 */
			if ( ! isset($_POST[$field]))
			{			
				if (in_array('isset', $ex, TRUE) OR in_array('required', $ex))
				{
					if ( ! isset($this->_error_messages['isset']))
					{
						if (FALSE === ($line = $this->CI->lang->line('isset')))
						{
							$line = 'The field was not set';
						}							
					}
					else
					{
						$line = $this->_error_messages['isset'];
					}
					
					// Build the error message
					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$message = sprintf($line, $mfield);

					// Set the error variable.  Example: $this->username_error
					$error = $field.'_error';
					$this->$error = $this->_error_prefix.$message.$this->_error_suffix;
					$this->_error_array[] = $message;
				}
						
				continue;
			}
	
			/*
			 * Set the current field
			 *
			 * The various prepping functions need to know the
			 * current field name so they can do this:
			 *
			 * $_POST[$this->_current_field] == 'bla bla';
			 */
			$this->_current_field = $field;

			// Cycle through the rules!
			foreach ($ex As $rule)
			{
				// Is the rule a callback?			
				$callback = FALSE;
				if (substr($rule, 0, 9) == 'callback_')
				{
					$rule = substr($rule, 9);
					$callback = TRUE;
				}
				
				// Strip the parameter (if exists) from the rule
				// Rules can contain a parameter: max_length[5]
				$param = FALSE;
				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
				{
					$rule	= $match[1];
					$param	= $match[2];
				}
				
				// Call the function that corresponds to the rule
				if ($callback === TRUE)
				{
					if ( ! method_exists($this->CI, $rule))
					{ 		
						continue;
					}
					
					$result = $this->CI->$rule($_POST[$field], $param);	
					
					// If the field isn't required and we just processed a callback we'll move on...
					if ( ! in_array('required', $ex, TRUE) AND $result !== FALSE)
					{
						continue 2;
					}
					
				}
				else
				{				
					if ( ! method_exists($this, $rule))
					{
						/*
						 * Run the native PHP function if called for
						 *
						 * If our own wrapper function doesn't exist we see
						 * if a native PHP function does. Users can use
						 * any native PHP function call that has one param.
						 */
						if (function_exists($rule))
						{
							$_POST[$field] = $rule($_POST[$field]);
							$this->$field = $_POST[$field];
						}
											
						continue;
					}
					
					$result = $this->$rule($_POST[$field], $param);
				}
								
				// Did the rule test negatively?  If so, grab the error.
				if ($result === FALSE)
				{
					if ( ! isset($this->_error_messages[$rule]))
					{
						if (FALSE === ($line = $this->CI->lang->line($rule)))
						{
							$line = 'Unable to access an error message corresponding to your field name.';
						}						
					}
					else
					{
						$line = $this->_error_messages[$rule];
					}				

					// Build the error message
					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$mparam = ( ! isset($this->_fields[$param])) ? $param : $this->_fields[$param];
					$message = sprintf($line, $mfield, $mparam);
					
					// Set the error variable.  Example: $this->username_error
					$error = $field.'_error';
					$this->$error = $this->_error_prefix.$message.$this->_error_suffix;

					// Add the error to the error array
					$this->_error_array[] = $message;				
					continue 2;
				}				
			}
			
		}
		
		$total_errors = count($this->_error_array);

		/*
		 * Recompile the class variables
		 *
		 * If any prepping functions were called the $_POST data
		 * might now be different then the corresponding class
		 * variables so we'll set them anew.
		 */	
		if ($total_errors > 0)
		{
			$this->_safe_form_data = TRUE;
		}
		
		$this->set_fields();

		// Did we end up with any errors?
		if ($total_errors == 0)
		{
			return TRUE;
		}
		
		// Generate the error string
		foreach ($this->_error_array as $val)
		{
			$this->error_string .= $this->_error_prefix.$val.$this->_error_suffix."\n";
		}

		return FALSE;
	}

}

?>
