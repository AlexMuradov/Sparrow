<?php

Namespace Localization;

Class Language {

    static $LngTitles = [
        "home" => "Home Page",
        "folder1/subfolder2" => "Subfolder2 Page"
    ];

    static $LngSomethingElse = [
        0 => ["Event1", "Event2"],
        1 => ["Test1", "Test2", "Test3"]
    ];

}

Class RU Extends Language {

    public function Import($__import) {

        if(is_array($__import)) {

            $output = array();
            foreach($__import as $k => $v) {
                $output[$v] = parent::$$v;
            }

            return $output;
        } else {
        return parent::$$__import;
        }
    }

}

?>
