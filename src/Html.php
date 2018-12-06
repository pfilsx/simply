<?php


namespace pfilsx\simply;


class Html
{
    protected static $counter = 0;


    public static function a($content, $link, $attributes = []){
        return static::tag('a', $content, array_merge($attributes, [
            'href' => $link
        ]));
    }

    public static function beginForm($attributes = []){
        return static::startTag('form', $attributes);
    }

    public static function endForm(){
        return static::endTag('form');
    }

    public static function label($content, $attributes = []){
        return static::tag('label', $content, $attributes);
    }

    public static function input($type, $name, $value = null, $attributes = []){
        return static::tag('input', null, array_merge($attributes, [
            'type' => $type,
            'name' => $name,
            'value' => $value
        ]));
    }

    public static function textInput($name, $value = null, $attributes = []){
        return static::tag('input', null, array_merge($attributes, [
            'type' => 'text',
            'name' => $name,
            'value' => $value
        ]));
    }

    public static function hiddenInput($name, $value = null, $attributes = []){
        return static::tag('input', null, array_merge($attributes, [
            'type' => 'hidden',
            'name' => $name,
            'value' => $value
        ]));
    }

    public static function passwordInput($name, $value = null, $attributes = []){
        return static::tag('input', null, array_merge($attributes, [
            'type' => 'password',
            'name' => $name,
            'value' => $value
        ]));
    }

    public static function emailInput($name, $value = null, $attributes = []){
        return static::tag('input', null, array_merge($attributes, [
            'type' => 'email',
            'name' => $name,
            'value' => $value
        ]));
    }

    public static function checkbox($name, $label, $isChecked = false, $attributes = []){
        $labelAttributes = [];
        if (array_key_exists('labelAttributes',$attributes)){
            $labelAttributes = (array)$attributes['labelAttributes'];
        }
        $hiddenId = 'cp'.static::$counter++;
        $hidden = static::hiddenInput($name, $isChecked == true ? 1 : 0, [
            'id' => $hiddenId
        ]);
        $onClickEvent = "document.getElementById('$hiddenId').value=this.checked?1:0;";
        if (array_key_exists('onclick', $attributes)){
            $onClickEvent .= ' '.$attributes['onclick'];
            unset($attributes['onclick']);
        }

        return $hidden.static::label(static::tag('input', null, array_merge($attributes, [
            'type' => 'checkbox',
            'checked' => $isChecked == true,
            'onclick' => $onClickEvent

        ])).$label, $labelAttributes);
    }

    public static function radio($name, $label, $value = null, $attributes = []){
        $attributes = static::prepareAttributes(array_merge($attributes, [
            'type' => 'radio',
            'name' => $name,
            'checked' => $value == true
        ]));
        return "<input $attributes >$label</input>";
    }



    public static function dropDown($name, $options, $value = null, $attributes = []){
        $content = static::generateOptions($options, $value);

        if (array_key_exists('prompt', $attributes)){
            $content = static::tag('option').PHP_EOL.$content;
        }

        return static::tag('select', $content, array_merge($attributes, [
            'name' => $name
        ]));
    }


    public static function textarea($name, $value = null, $attributes = []){
        return static::tag('textarea', $value, array_merge($attributes, [
            'name' => $name
        ]));
    }

    public static function startTag($name, $options = []){
        $attributes = static::prepareAttributes($options);
        if (in_array($name, static::$singleTags)){
            return "<$name $attributes />";
        } else {
            return "<$name $attributes >";
        }
    }

    public static function endTag($name){
        if (in_array($name, static::$singleTags)){
            return "";
        } else {
            return "</$name>";
        }
    }

    public static function tag($name, $content = null, $attributes = [])
    {
        if (in_array($name, static::$singleTags)){
            return static::startTag($name, $attributes);
        } else {
            return implode(PHP_EOL, [
                static::startTag($name, $attributes),
                $content,
                static::endTag($name)]
            );
        }
    }

    private static function generateOptions($options, $value){
        $result = [];
        foreach ($options as $key => $val){
            $result[] = static::tag('option', $val, [
                'value' => $key,
                'selected' => $key == $value ? true : null
            ]);
        }
        return implode(PHP_EOL, $result);
    }

    private static function prepareAttributes($attributes){
        $attrs = [];
        foreach ($attributes as $key => $value){
            if ($value === null || $value === false){
                continue;
            }
            if ($value === true){
                $attrs[] = $key;
            } else {
                $value = htmlentities($value);
                $attrs[] = "$key=\"$value\"";
            }
        }
        return implode(' ', $attrs);
    }

    private static $singleTags = [
        'area',
        'base',
        'basefont',
        'bgsound',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'isindex',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr'
    ];

}