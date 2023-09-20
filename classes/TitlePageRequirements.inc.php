<?php

class TitlePageRequirements
{
    public const CPDF_PATH = "/usr/local/bin/cpdf";

    public function checkRequirements()
    {
        return $this->checkPdfManipulator();
    }

    private function checkPdfManipulator()
    {
        if(!file_exists(self::CPDF_PATH)) {
            $this->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.requirements.pdfManipulatorMissing');
            return false;
        }

        if (!is_executable(self::CPDF_PATH)) {
            $this->showMissingRequirementNotification('plugins.generic.titlePageForPreprint.requirements.pdfManipulatorNotExecutable');
            return false;
        }
        return true;
    }

    public function showMissingRequirementNotification($notificationMessage)
    {
        $currentUser = Application::get()->getRequest()->getUser();
        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification($currentUser->getId(), NOTIFICATION_TYPE_WARNING, array('contents' => __($notificationMessage)));
    }
}
