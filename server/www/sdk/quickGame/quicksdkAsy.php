<?php

/**
 * QuickSDK��Ϸͬ���ӽ����㷨����
 * @copyright quicksdk 2015
 * @author quicksdk 
 * @version quicksdk v 0.0.1 2014/9/2
 */

class quickAsy{

	/**
	 * ���ܷ���
	 * $strEncode ����
	 * $keys ������Կ Ϊ��Ϸ����ʱ����� callback_key
	 */
	public function decode($strEncode, $keys) {
		if(empty($strEncode)){
			return $strEncode;
		}
		preg_match_all('(\d+)', $strEncode, $list);
		$list = $list[0];
		if (count($list) > 0) {
			$keys = self::getBytes($keys);
			for ($i = 0; $i < count($list); $i++) {
				$keyVar = $keys[$i % count($keys)];
				$data[$i] =  $list[$i] - (0xff & $keyVar);
			}
			return self::toStr($data);
		} else {
			return $strEncode;
		}
	}
	
	/**
	 * ������Ϸͬ��ǩ��
	 */
	public static function getSign($params,$md5key){

		return md5($params['nt_data'].$params['sign'].$md5key);
	}
	
	
	
	/**
	 * ת���ַ�����
	 */
	private static function getBytes($string) {  
        $bytes = array();  
        for($i = 0; $i < strlen($string); $i++){  
             $bytes[] = ord($string[$i]);  
        }  
        return $bytes;  
    }  
    
    /**
     * ת���ַ���
     */
    private static function toStr($bytes) {  
        $str = '';  
        foreach($bytes as $ch) {  
            $str .= chr($ch);  
        }  
   		return $str;  
    }
}

?>