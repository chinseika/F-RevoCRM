<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Text_UIType extends Vtiger_Base_UIType {

	/**
	 * Function to get the Display Value, for the current field type with given DB Insert Value
	 * @param <Object> $value
	 * @return <Object>
	 */
	public function getDisplayValue($value, $record=false, $recordInstance = false,$removeTags = false) {
		if(in_array($this->get('field')->getFieldName(),array('signature','commentcontent'))) {
			return $value;
		}
                if($removeTags){
                    $value = strip_tags($value,'<br>');
                    return nl2br(purifyHtmlEventAttributes($value, true));
                }
		return self::linkifyUrls(nl2br(purifyHtmlEventAttributes($value, true)));
	}

	/**
	 * テキスト中のURLをリンク表示に変換する（既存タグ内は対象外、全角文字・閉じ括弧はURLに含めない） #1773
	 * @param <String> $value
	 * @return <String>
	 */
	public static function linkifyUrls($value) {
		$pattern = '/<[^>]*>|(https?:\/\/[^\s<>"\'\x{3000}-\x{30FF}\x{4E00}-\x{9FFF}\x{FF00}-\x{FFEF})\]]+)/u';
		$result = preg_replace_callback($pattern, function($matches) {
			if(empty($matches[1])) {
				return $matches[0];
			}
			$url = rtrim($matches[1], '.,');
			$trail = substr($matches[1], strlen($url));
			return '<a class="urlField cursorPointer" href="'.$url.'" target="_blank">'.$url.'</a>'.$trail;
		}, $value);
		// 不正なUTF-8バイト列を含む値ではnullが返るため、元の値をそのまま表示する
		return $result === null ? $value : $result;
	}
    
    /**
	 * Function to get the Template name for the current UI Type Object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/Text.tpl';
	}
}