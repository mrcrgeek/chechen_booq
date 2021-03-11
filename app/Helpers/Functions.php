<?php

function connect(){
	$connect=mysqli_connect('localhost','root','');
	mysqli_query($connect,"SET NAMES 'utf8'" );
	mysqli_query($connect,"SET CHARACTER SET 'utf8'");
	mysqli_query($connect,"SET character_set_connection='utf8'");
	mysqli_select_db($connect,'chechen_booq');
	return $connect;
}

function sis(&$input = null, $default = null,$not_be=[])
{
    $ret = $default;
    if (isset($input)) 
    {
        if(!in_array($input,$not_be))
        {
            $ret = $input;
        }
    }
    return $ret;
}

function sanitize($text)
{
    $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
    return $text;
}

function redundant_remover ($input) 
{

	/*
	$available_types = [
		"string",
		"ir_phone_number",
		"email",
		"number",
		"file" // checks if file exists
	];
	*/

	/*
	// syntax
	$input = [
		"value" => "value" // (could be string or number(integer, float))
		"type" => "required format of result listed in $available_types",
		"options" => array() // (optional) you can customize the result with this property. for example you can set min_length and max_length limit to string
	];
	*/

	$result = null;

	$options = [
		'default_max_length' => sis($options['default_max_length'], 10000),
		'default_min_length' => sis($options['default_min_length'], 1),
		'default_min_number' => sis($options['default_min_number'], 1),
		'default_max_number' => sis($options['default_max_number'], 1000000000),
		'default_type' => sis($options['default_type'], 'string'),
	];

	if ($input['type'] == "string")
	{

		$input['max_length'] = sis($input['max_length'], $options['default_max_length']);
		$input['min_length'] = sis($input['min_length'], $options['default_min_length']);

		$string_length = strlen($input['value']);
		if ( $string_length >= $input['min_length'] && $string_length <= $input['max_length'] )
		{
			$result = $input['value'];
		}

	}
	else if ($input['type'] == 'ir_phone_number')
	{
		
		$phone_number=persian_number_to_english_number(sis($input['value']));
		preg_match('/09\d{9}/', $phone_number, $phone_number_array);
		$phone_number=sis($phone_number_array['0']);
		if($phone_number!=NULL)
		{
			$result = $phone_number;
		}

	}
	else if ($input['type'] == 'email') 
	{
		if (filter_var($input['value'], FILTER_VALIDATE_EMAIL)) {
			$result = $input['value'];
		}
	}
	else if ($required_array['type'] == "number")
	{
		$number_options = sis($input['options'], []);
		$input['min_number'] = sis($input['min_number'], $options['default_min_number']);
		$input['max_number'] = sis($input['max_number'], $options['default_max_number']);

		$input['value'] = make_it_number($input['value'], $number_options);
		if ( $input['value'] >= $input['min_number'] && $input['value'] <= $input['max_number'] )
		{
			$result = $input['value'];
		}
	}
	else if ($input['type'] == 'file')
	{
		if (file_exists($input['value']))
		{
			$result = $input['value'];
		}
		/*
		else 
		{
			var_dump_pre("{$input['value']} doesn't exists");
		}
		*/
	}

	return $result;

}

function encryptthis($data, $key='uguywgyweiqkwjdiw239&&kdafweih@#$%$#$#^$ihiwyhdhjwuuO')
{
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decryptthis($data, $key='uguywgyweiqkwjdiw239&&kdafweih@#$%$#$#^$ihiwyhdhjwuuO')
{
    $encryption_key = base64_decode($key);
    list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

function display_result($result){
	
	$result = [
		"result" => sis($result['result'], []),
		"http_code" => sis($result['http_code'], 200)
	];
	
	header("HTTP/1.1 {$result['http_code']}", true, $result['http_code']);
	echo json_encode($result['result']);
}
?>