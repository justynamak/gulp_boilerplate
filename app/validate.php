<?php

class Validate {
    private $errors = [];

    public function getErrors() {

        return $this->errors;
    }

    public function phone($str) {
        return filter_var($str, FILTER_VALIDATE_REGEXP, ["options" => [
            "regexp" => "/(?<!\w)(\(?(\+|00)?48\)?)?[ -]?\d{3}[ -]?\d{3}[ -]?\d{3}(?!\w)/"
        ]]);
    }

    public function string($str) {
        return filter_var($str, FILTER_VALIDATE_REGEXP, ["options" => [
            "regexp" => "/^[a-zA-ZęóąśłżźćńĘÓĄŚŁŻŹĆŃ -]{3,}$/"
        ]]);
    }

    public function email($str) {
        return filter_var($str, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    public function length($str, $cond) {
        if( !is_array($cond) ){
            return strlen($str) === $cond;
        }

        $result = false;

        if (isset($cond['min'])) {
            if (strlen($str) >= $cond['min']) {
                $result = true;
            } else {
                return false;
            }
        }

        if (isset($cond['max'])) {
            if (strlen($str) <= $cond['max']) {
                $result = true;
            } else {
                return false;
            }
        }

        return $result;
    }
    
    function pesel( $value, $return = false ){
		$str = trim($value);

		if( !preg_match('/^[0-9]{11}$/',$str) ){
			return false;
		}
	
		// tablica z odpowiednimi wagami
		$arrSteps = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
		
		$intSum = 0;
		
		for ($i = 0; $i < 10; $i++){
			//mnożymy każdy ze znaków przez wagć i sumujemy wszystko
			$intSum += $arrSteps[$i] * $str[$i];
		}
		
		//obliczamy sumć kontrolną
		$int = 10 - $intSum % 10; 
		
		$intControlNr = ($int == 10)?0:$int;
		
		//sprawdzamy czy taka sama suma kontrolna jest w ciągu
		if( $intControlNr == $str[10] ){
			return true;
		}

		return false;
	}

    public function file($file, $file_size_limit = false, array $file_valid_type = NULL) {
        if ($file_size_limit) {
            if ($file['size'] > $file_size_limit) {
                $this->errors['file'][] = 'Plik jest zbyt duży';
                return false;
            }
        }

        if( $file_valid_type ){
            if(!$this->validateFileMimes($file, $file_valid_type)) {
                $this->errors['file'][] = 'Błędny format pliku';
                return false;
            }
        }

        return true;
    }
    
    public function recaptcha($secret, $response) {
	    // $response = $_POST['g-recaptcha-response'];
	    $remoteip = $_SERVER['REMOTE_ADDR'];
	    
	    $url = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip");

	    $result = json_decode($url, TRUE);
	    
	    if ($result['success'] == 1) {
	      return true;
	    }else{
	      return false;
	    }
    }

    private function validateFileMimes($file, $file_valid_type) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $file_mime = $finfo->file($file['tmp_name']);

        $file_mimes = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'xml' => 'text/xml',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel'
        ];

        $tmp_mime = [];

        foreach ( $file_valid_type as $file_type ){
            $tmp_mime[] = $file_mimes[$file_type];
        }

        if (in_array($file_mime, $tmp_mime)) {
            return true;
        }else{
            return false;
        }
    }

}