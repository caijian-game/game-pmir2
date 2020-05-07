<?php
namespace App\Controller\Game;

/**
 *
 */
class Settings
{
    public $baseStats = [
        0 => [
            'HpGain'              => 4,
            'HpGainRate'          => 4.5,
            'MpGainRate'          => 0,
            'BagWeightGain'       => 3,
            'WearWeightGain'      => 20,
            'HandWeightGain'      => 13,
            'MinAc'               => 0,
            'MaxAc'               => 7,
            'MinMac'              => 0,
            'MaxMac'              => 0,
            'MinDc'               => 5,
            'MaxDc'               => 5,
            'MinMc'               => 0,
            'MaxMc'               => 0,
            'MinSc'               => 0,
            'MaxSc'               => 0,
            'StartAgility'        => 15,
            'StartAccuracy'       => 5,
            'StartCriticalRate'   => 0,
            'StartCriticalDamage' => 0,
            'CritialRateGain'     => 0,
            'CriticalDamageGain'  => 0,
        ],
        1 => [
            'HpGain'              => 15,
            'HpGainRate'          => 1.8,
            'MpGainRate'          => 0,
            'BagWeightGain'       => 5,
            'WearWeightGain'      => 100,
            'HandWeightGain'      => 90,
            'MinAc'               => 0,
            'MaxAc'               => 0,
            'MinMac'              => 0,
            'MaxMac'              => 0,
            'MinDc'               => 7,
            'MaxDc'               => 7,
            'MinMc'               => 7,
            'MaxMc'               => 7,
            'MinSc'               => 0,
            'MaxSc'               => 0,
            'StartAgility'        => 15,
            'StartAccuracy'       => 5,
            'StartCriticalRate'   => 0,
            'StartCriticalDamage' => 0,
            'CritialRateGain'     => 0,
            'CriticalDamageGain'  => 0,
        ],
        2 => [
            'HpGain'              => 6,
            'HpGainRate'          => 2.5,
            'MpGainRate'          => 0,
            'BagWeightGain'       => 4,
            'WearWeightGain'      => 50,
            'HandWeightGain'      => 42,
            'MinAc'               => 0,
            'MaxAc'               => 0,
            'MinMac'              => 12,
            'MaxMac'              => 6,
            'MinDc'               => 7,
            'MaxDc'               => 7,
            'MinMc'               => 0,
            'MaxMc'               => 0,
            'MinSc'               => 7,
            'MaxSc'               => 7,
            'StartAgility'        => 18,
            'StartAccuracy'       => 5,
            'StartCriticalRate'   => 0,
            'StartCriticalDamage' => 0,
            'CritialRateGain'     => 0,
            'CriticalDamageGain'  => 0,
        ],
    ];

    public function getBaseStats($class)
    {
        return $this->baseStats[$class];
    }

    //灯光设置 TODO 定时器控制
    public function lightSet($light = null)
    {
        if (!empty($light)) {
            return intval($light);
        }

        $date = date('H');

        $light = 0;

        $Enum = getObject('Enum');

        switch ($date) {
            case $date >= 5 && $date < 8:
                $light = $Enum::LightSettingDawn;
                break;

            case $date >= 8 && $date < 17:
                $light = $Enum::LightSettingDay;
                break;

            case $date >= 17 && $date < 20:
                $light = $Enum::LightSettingEvening;
                break;

            case $date >= 20 || $date < 5:
                // $light = $Enum::LightSettingNight; //太黑了 改为傍晚
                $light = $Enum::LightSettingEvening;
                break;

            default:
                $light = $Enum::LightSettingNormal;
                break;
        }

        return intval($light);
    }
}
