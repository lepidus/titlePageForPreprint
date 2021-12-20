<?php

class TitlePageRequirements {

    const CPDF_PATH = __DIR__ . "/../tools/cpdf";

    public function checkPdfManipulator() {
		if(is_executable(self::CPDF_PATH)) {
            $this->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.binaryShouldBeExecutable');
            return false;
        }
        return true;
	}

    private function showMissingRequirementNotification($notificationMessage) {
        $currentUser = Application::get()->getRequest()->getUser();
        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification($currentUser->getId(), NOTIFICATION_TYPE_WARNING, array('contents' => __($notificationMessage)));
    }

}