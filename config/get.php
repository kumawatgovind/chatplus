<?php use Symfony\Component\Yaml\Yaml;

if (file_exists(storage_path('config')."/settings.yml")) {
    $settings = Yaml::parse(file_get_contents(storage_path('config')."/settings.yml"));
    if(!empty($settings)){
        return $settings;
    }
}
