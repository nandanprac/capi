<?php
/**
 * Created by PhpStorm.
 * User: anshuman
 * Date: 04/05/15
 * Time: 18:26
 */

namespace ConsultBundle\Manager;

use ConsultBundle\Constants\ConsultConstants;
use ConsultBundle\Entity\DoctorQuestion;
use ConsultBundle\Utility\Utility;
use Doctrine\Common\Collections\ArrayCollection;
use ConsultBundle\Manager\ValidationError;

/**
 * Doctor manager
 */
class DoctorManager extends BaseManager
{
    /**
     * @param array $queryParams
     *
     * @return array|null
     * @throws \Exception
     */
    public function loadAllForDoctor($queryParams)
    {
		$doctorId = array_key_exists('practo_account_id', $queryParams) ? $queryParams['practo_account_id'] : null;

		if (null == $doctorId) {
			throw new \Exception(array("error"=>"Please pass practo_account_id"));
		}

        try {
            $detailList = $this->getRepository()->findByFilters($doctorId, $queryParams);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if (null == $detailList) {
            return null;
        }

        return array("details"=>$detailList);
	}

	private function getRepository()
    {

        return $this->helper->getRepository(ConsultConstants::DOCTOR_SETTING_ENTITY_NAME);
    }

}
