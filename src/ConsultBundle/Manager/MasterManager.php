<?php

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultFeatureData;

/**
 * Notification Manager at Consult
 */
class MasterManager extends BaseManager
{
    /**
     * @return array
     */
    public function loadMasterSpecialties()
    {
        return ConsultFeatureData::$MASTERSPECIALITIES;
    }

    /**
     * @return array
     */
    public function loadOccupationOptions()
    {
        return ConsultFeatureData::$OCCUPATIONOPTIONS;
    }
}
