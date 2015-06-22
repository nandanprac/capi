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
		return ConsultFeatureData::$MASTER_SPECIALITIES;
    }

    /**
     * @return array
     */
    public function loadOccupationOptions()
    {
	return ConsultFeatureData::$OCCUPATION_OPTIONS;
    }
}
