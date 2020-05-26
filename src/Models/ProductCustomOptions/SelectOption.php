<?php

namespace Xpressengine\Plugins\XeroCommerce\Models\ProductCustomOptions;

use Xpressengine\Plugins\XeroCommerce\Models\ProductCustomOption;

class SelectOption extends ProductCustomOption
{

    public static $singleTableType = 'select';

    public static $singleTableName = '목록선택';

    public function renderInputHtml(array $attrs = [])
    {
        $result = '<select ';
        if(!array_has($attrs, 'name')) {
            $result .= "name=\"custom_options[{$this->id}][value]\" ";
        }
        foreach ($attrs as $key => $value) {
            $result .= "$key=\"$value\" ";
        }
        if($this->is_required) {
            $result .= 'required ';
        }
        $result .= '>';
        if(!empty($this->settings['options'])) {
            foreach ($this->settings['options'] as $key => $value) {
                $result .= "<option value=\"$key\">$value</option>";
            }
        }
        $result .= '</select>';
        return $result;
    }

}
