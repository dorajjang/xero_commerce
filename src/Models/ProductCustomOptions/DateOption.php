<?php

namespace Xpressengine\Plugins\XeroCommerce\Models\ProductCustomOptions;

class DateOption extends TextOption
{

    public static $singleTableType = 'date';

    public static $singleTableName = '날짜';

    public function renderValueInput(array $attrs)
    {
        $result = '<input type="date" ';
        foreach ($attrs as $key => $value) {
            $result .= "$key=\"$value\" ";
        }
        if($this->is_required) {
            $result .= 'required ';
        }
        $result .= '/>';
        return $result;
    }

}
