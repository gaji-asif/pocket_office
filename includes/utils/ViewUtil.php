<?php

/**

 * @author cmitchell

 */

class ViewUtil extends AssureUtil {



    public static function loadView($view, $viewData = NULL) {

        $path = ROOT_PATH . '/includes/views/' . $view . '-view.php';



        if (file_exists($path)) {

            if (is_array($viewData)) {

                extract($viewData, EXTR_OVERWRITE);

            }



            ob_start();

            include($path);

            $contents = ob_get_clean();



            return $contents;

        }

    }



    public static function generatePicklist($options, $valueKey, $labelKey, $selectedVal = NULL, $attributes = NULL) {

        $viewData = array(

            'options' => $options,

            'value_key' => $valueKey,

            'label_key' => $labelKey,

            'selected_value' => $selectedVal,

            'attributes' => $attributes

        );



        return self::loadView('controls/picklist', $viewData);

    }

    

}