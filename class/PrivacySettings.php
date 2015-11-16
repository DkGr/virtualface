<?php

/**
 * Description of PrivacySettings
 *
 * @author padman
 */

abstract class VisibilityType
{
    const Me = 0;
    const Friends = 1;
    const Everybody = 2;
}

class PrivacySettings {    
    private static $defaultSettings = array(
                "displayname" => VisibilityType::Everybody, 
                "email" => VisibilityType::Me, 
                "friends" => VisibilityType::Everybody
            );
    
    public static function getDefaultPrivacySettings()
    {
        return PrivacySettings::$defaultSettings;
    }
    
    public static function changePrivacySetting($settings, $settingToChange, $visibilityType)
    {
        $settingsObj = json_decode($settings);
        $settings[$settingToChange] = $visibilityType;
        return json_encode($settings);
    }
}
